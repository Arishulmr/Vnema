<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Channel;
use App\Models\Video;
use App\Models\Livestream; // Missing import
use App\Models\Vtuber;
use App\Providers\YouTubeProvider;

class FetchYouTubeClips extends Command
{
    protected $signature = 'fetch:youtube-clips';
    protected $description = 'Fetch VTuber clips with English subtitles';

    protected $youTubeProvider; // Declare the property

    public function __construct(YouTubeProvider $youTubeProvider)
    {
        parent::__construct();
        $this->youTubeProvider = $youTubeProvider; // Initialize the property
    }

    public function handle()
{
    $channels = Channel::pluck('youtube_channel_id')->toArray();
    $keywords = ['Eng Sub', '[English Sub]', 'English Sub', 'ENG SUBS', '(ENG SUBS)'];
    $vtubers = Vtuber::all();


    $videos = $this->youTubeProvider->searchClips($keywords, $channels);

    if (!is_array($videos) || !isset($videos['items'])) {
        $this->error('Invalid response from YouTube API.');
        return;
    }


    // Debugging: Print fetched videos before saving
    dd($videos['items']);

    $livestreams = Livestream::pluck('youtube_livestream_id')->toArray();

    foreach ($videos['items'] as $video) {
        $livestreamId = null;
        foreach ($livestreams as $ls) {
            if (str_contains($video['snippet']['description'] ?? '', $ls)) {
                $livestreamId = Livestream::where('youtube_livestream_id', $ls)->first()?->id;
                break;
            }
        }


        Video::updateOrCreate(
            ['youtube_video_id' => $video['id']['videoId'] ?? null],
            [
                'title' => $video['snippet']['title'] ?? 'Unknown Title',
                'description' => $video['snippet']['description'] ?? '',
                'thumbnail_url' => $video['snippet']['thumbnails']['high']['url'] ?? '',
                'channel_id' => Channel::where('youtube_channel_id', $video['snippet']['channelId'] ?? null)->first()?->id,
                'livestream_id' => $livestreamId

            ]
        );
    }

    $this->updateVtuberIds();


    $this->info('Fetched YouTube Clips Successfully');
}

protected function updateVtuberIds()
{
    $vtubers = Vtuber::all();
    foreach ($vtubers as $vtuber) {
        $videos = Video::where('title', 'LIKE', "%{$vtuber->name}%")->get();
        foreach ($videos as $video) {
            $video->vtubers()->syncWithoutDetaching([$vtuber->id]);
        }

    }
}


}