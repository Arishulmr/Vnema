<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vtuber;
use App\Models\Livestream;
use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchPastLivestreams extends Command
{
    protected $signature = 'livestreams:fetch-past';
    protected $description = 'Fetch past livestreams from YouTube API and update the database';

    public function handle()
    {
        $vtubers = Vtuber::all();
        $channels = Channel::all();

        foreach ($vtubers as $vtuber) {
            if (!$vtuber->channelUrl) continue;

            $channelId = $this->extractChannelId($vtuber->channelUrl);
            if (!$channelId) continue;

            $videos = $this->fetchVideos($channelId);
            foreach ($videos as $video) {
                $livestreamId = $this->extractLivestreamId($video['description']);
                if ($livestreamId) {
                    Livestream::updateOrCreate(
                        ['youtube_livestream_id' => $livestreamId],
                        [
                            'title'       => $video['title'],
                            // 'description' => $video['description'],
                            'vtuber_id'   => $vtuber->id,
                        ]
                    );
                }
            }
        }

        Log::info('Past livestreams fetched successfully.');
    }

    private function extractChannelId($channelUrl)
    {
        preg_match('/youtube\.com\/channel\/([a-zA-Z0-9_-]+)/', $channelUrl, $matches);
        return $matches[1] ?? null;
    }

    private function fetchVideos($channelId)
    {
        $apiKey = config('services.youtube.api_key');
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channelId}&type=video&maxResults=50&key={$apiKey}";

        $response = Http::get($url);
        if ($response->failed()) return [];

        return collect($response->json()['items'])->map(function ($item) {
            return [
                'id'          => $item['id']['videoId'],
                'title'       => $item['snippet']['title'],
                'description' => $item['snippet']['description'] ?? '',
            ];
        })->toArray();
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
}
