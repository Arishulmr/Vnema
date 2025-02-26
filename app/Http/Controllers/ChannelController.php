<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Event;
use App\Models\Video;
use App\Models\Livestream;
use App\Models\Vtuber;
use Illuminate\Support\Facades\DB;
use App\Providers\YouTubeProvider;
use Illuminate\Support\Facades\Artisan;

class ChannelController extends Controller
{
    protected $youTubeProvider;

    public function __construct(YouTubeProvider $youTubeProvider)
    {
        $this->youTubeProvider = $youTubeProvider;
    }

    // Show the dashboard
    public function index()
    {
        $channels = Channel::all();
        return view('dashboard.channels.index', compact('channels'));
    }

    // Search for YouTube channels
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = $this->youTubeProvider->searchChannels($query);

        return response()->json($results);
    }

    public function destroy($id)
{
    $channel = Channel::findOrFail($id);

    // 🗑️ Delete all videos related to this channel
    Video::where('channel_id', $channel->id)->delete();

    // 🗑️ Delete the channel itself
    $channel->delete();

    return redirect()->back()->with('success', 'Channel and its videos deleted successfully.');
}

private function extractLivestreamId($description)
    {
        // Match both direct and embedded links
        if (preg_match('/https:\/\/www\.youtube\.com\/live\/([a-zA-Z0-9_-]+)/', $description, $matches)) {
            return $matches[1];
        }
        if (preg_match('/<a\s+href="https:\/\/www\.youtube\.com\/live\/([a-zA-Z0-9_-]+)"/', $description, $matches)) {
            return $matches[1];
        }
        if (preg_match('/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $description, $matches)) {
            return $matches[1];
        }
        if (preg_match('/<a\s+href="https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)"/', $description, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function findMatchingEvent($title, $description)
    {
        $events = Event::all();

        foreach ($events as $event) {
            $keywords = explode(',', $event->description); // Keywords stored as comma-separated values

            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (str_contains(strtolower($title), strtolower($keyword)) ||
                    str_contains(strtolower($description), strtolower($keyword))) {
                    return $event->id; // Return first matching event
                }
            }
        }

        return null; // No event matched
    }

    // Add a channel and fetch its videos
    public function add(Request $request)
    {
    ini_set('max_execution_time', 300);
    $vtubers = Vtuber::all();
    $videoVtuberRelations = [];

        $channelData = $request->validate([
            'youtube_channel_id' => 'required|string|unique:channels,youtube_channel_id',
            'name' => 'required|string',
        ]);

        $channel = Channel::create([
            'youtube_channel_id' => $channelData['youtube_channel_id'],
            'name' => $channelData['name']
        ]);

        // Fetch all videos from the added channel
        $videos = $this->youTubeProvider->fetchChannelVideos($channel->youtube_channel_id);
        foreach ($videos as $video) {
            $addedvideo = Video::updateOrCreate(
                ['youtube_video_id' => $video['id']],
                [
                    'title' => $video['title'],
                    'description' => $video['description'],
                    'thumbnail_url' => $video['thumbnail'],
                    'channel_id' => $channel->id,
                ]
            );
            $livestreamId = $this->extractLivestreamId($video['description']);
                if ($livestreamId) {
                    $uploaderChannelId = $video['uploader_channel_id'] ?? null; // Adjust based on actual API response

                    if ($uploaderChannelId) {
            // Find the VTuber whose official channel matches the uploader's channel ID
                         $vtuber = Vtuber::where('channelUrl', 'LIKE', "%{$uploaderChannelId}%")->first();
                    }
                    $livestreamDetails = $this->youTubeProvider->fetchLivestreamDetails($livestreamId);
                    if ($livestreamDetails) {
                        $livestream = Livestream::updateOrCreate(
                            ['youtube_livestream_id' => $livestreamId],
                            [
                                'title'       => $livestreamDetails['title'], // Use the livestream's title
                                'description' => $livestreamDetails['description'], // Use the livestream's description
                                'vtuber_id'   => $vtuber->id ?? null, // Assign correct vtuber_id
                            ]
                        );
                      $livestream->videos()->attach($addedvideo->id);
                    }
                }
        }
        foreach ($vtubers as $vtuber) {

            $parts = explode(' ', $vtuber->name);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';

            $pattern = "[[:<:]]" . str_replace(' ', '[[:space:]]*', $vtuber->name) . "[[:>:]]";
            if (!empty($lastName)) {
            $pattern .= "|[[:<:]]{$lastName}[[:>:]]"; // Match last name as a full word
}
            $videoIds = Video::whereRaw("title REGEXP ? OR description REGEXP ?", [$pattern, $pattern])
                     ->pluck('id');

                     foreach ($videoIds as $videoId) {
                        $videoVtuberRelations[] = ['video_id' => $videoId, 'vtuber_id' => $vtuber->id];
                    }

        }

        if (!empty($videoVtuberRelations)) {
            DB::table('video_vtuber')->insertOrIgnore($videoVtuberRelations);
        }

        // Artisan::call('livestreams:fetch-past');
        return redirect()->route('dashboard')->with('success', 'Channel added and videos fetched successfully.');
    }


}
