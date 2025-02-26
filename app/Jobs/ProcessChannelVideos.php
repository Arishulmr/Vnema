<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Video;
use App\Models\Livestream;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProcessChannelVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channelId;

    public function __construct($channelId)
    {
        $this->channelId = $channelId;
    }

    public function handle()
    {
        $channel = Channel::find($this->channelId);
        if (!$channel) return;

        $vtubers = DB::table('vtubers')->pluck('id', 'channelUrl');
        $videoVtuberRelations = [];
        $livestreamVideoRelations = [];

        $videos = app('YouTubeProvider')->fetchChannelVideos($channel->youtube_channel_id);

        // Process videos in small batches to prevent timeout
        $batchSize = 50;
        $videoChunks = array_chunk($videos, $batchSize);

        foreach ($videoChunks as $videoBatch) {
            $videosToInsert = [];
            $livestreamsToInsert = [];

            foreach ($videoBatch as $video) {
                $videosToInsert[$video['id']] = [
                    'youtube_video_id' => $video['id'],
                    'title' => $video['title'],
                    'description' => $video['description'],
                    'thumbnail_url' => $video['thumbnail'],
                    'channel_id' => $channel->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $livestreamId = $this->extractLivestreamId($video['description']);
                if ($livestreamId) {
                    $uploaderChannelId = $video['uploader_channel_id'] ?? null;
                    $vtuberId = $vtubers[$uploaderChannelId] ?? null;

                    $livestreamDetails = Cache::remember("livestream_{$livestreamId}", 3600, function () use ($livestreamId) {
                        return app('YouTubeProvider')->fetchLivestreamDetails($livestreamId);
                    });

                    if ($livestreamDetails) {
                        $livestreamsToInsert[$livestreamId] = [
                            'youtube_livestream_id' => $livestreamId,
                            'title' => $livestreamDetails['title'],
                            'description' => $livestreamDetails['description'],
                            'vtuber_id' => $vtuberId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $livestreamVideoRelations[] = [
                            'livestream_id' => $livestreamId,
                            'video_id' => $video['id']
                        ];
                    }
                }
            }

            DB::transaction(function () use ($videosToInsert, $livestreamsToInsert, $livestreamVideoRelations) {
                Video::insertOrIgnore(array_values($videosToInsert));
                Livestream::insertOrIgnore(array_values($livestreamsToInsert));
                DB::table('livestream_video')->insertOrIgnore($livestreamVideoRelations);
            });
        }
    }

    private function extractLivestreamId($description)
    {
        if (preg_match('/https:\/\/www\.youtube\.com\/live\/([a-zA-Z0-9_-]+)/', $description, $matches)) {
            return $matches[1];
        }
        if (preg_match('/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $description, $matches)) {
            return $matches[1];
        }
        return null;
    }
}