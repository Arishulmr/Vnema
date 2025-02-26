<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\YouTubeProvider;
use App\Models\Channel;
use App\Models\Livestream;
use App\Models\Video;
use App\Models\Vtuber;
use Illuminate\Support\Facades\Artisan;

class FetchNewVideos extends Command
{
    protected $signature = 'fetch:youtube-new-videos';
    protected $description = 'Fetch new videos from added channels every hour';

    protected $youTubeProvider;

    public function __construct(YouTubeProvider $youTubeProvider)
    {
        parent::__construct();
        $this->youTubeProvider = $youTubeProvider;
    }

    public function handle()
    {
        $channels = Channel::pluck('youtube_channel_id')->toArray();

        foreach ($channels as $channelId) {
            $videos = $this->youTubeProvider->fetchChannelVideos($channelId); // Fetch all videos from channel

            if (empty($videos)) {
                $this->warn("No new videos found for channel: $channelId");
                continue;
            }

            foreach ($videos as $video) {
                $channel = Channel::where('youtube_channel_id', $channelId)->first();
                if (!$channel) {
                    $this->error("Channel not found for ID: $channelId");
                    continue;
                }

                $addedVideo = Video::updateOrCreate(
                    ['youtube_video_id' => $video['id']],
                    [
                        'title' => $video['title'],
                        'description' => $video['description'],
                        'thumbnail_url' => $video['thumbnail'],
                        'channel_id' => $channel->id,
                    ]
                );

                // Extract and associate livestream if exists
                $livestreamId = $this->extractLivestreamId($video['description']);
                if ($livestreamId) {
                    $livestreamDetails = $this->youTubeProvider->fetchLivestreamDetails($livestreamId);

                    if ($livestreamDetails) {
                        $livestream = Livestream::updateOrCreate(
                            ['youtube_livestream_id' => $livestreamId],
                            [
                                'title'       => $livestreamDetails['title'],
                                'description' => $livestreamDetails['description'],
                            ]
                        );
                        $livestream->videos()->attach($addedVideo->id);
                    }
                }
            }
        }

        // Run VTuber update logic after fetching new videos
        $this->updateVtuberVideos();
        // Artisan::call('livestreams:fetch-past');

        $this->info('New videos fetched and updated successfully!');
    }

    private function updateVtuberVideos()
    {
        $vtubers = Vtuber::all();

        foreach ($vtubers as $vtuber) {
            $videos = Video::where('title', 'LIKE', "%{$vtuber->name}%")->get();
            foreach ($videos as $video) {
                $video->vtubers()->syncWithoutDetaching([$vtuber->id]);
            }
        }

        $this->info("Videos updated successfully!");
    }

    private function extractLivestreamId($description)
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtube\.com\/live\/)([\w-]{11})/', $description, $matches);
        return $matches[1] ?? null;
    }
}