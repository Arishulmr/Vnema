<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Video;
use App\Providers\YouTubeProvider;

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
        return view('dashboard', compact('channels'));
    }

    // Search for YouTube channels
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = $this->youTubeProvider->searchChannels($query);

        return response()->json($results);
    }

    // Add a channel and fetch its videos
    public function add(Request $request)
    {
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
            Video::updateOrCreate(
                ['youtube_video_id' => $video['id']],
                [
                    'title' => $video['title'],
                    'description' => $video['description'],
                    'thumbnail_url' => $video['thumbnail'],
                    'channel_id' => $channel->id,
                    'livestream_id' => null
                ]
            );
        }

        return redirect()->route('dashboard')->with('success', 'Channel added and videos fetched successfully.');
    }
}