<?php

namespace App\Providers;

use App\Models\Livestream;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class YouTubeProvider extends ServiceProvider
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
    }

    public function fetchLivestreamDetails($livestreamId)
    {
        $url = "https://www.googleapis.com/youtube/v3/videos?id={$livestreamId}&part=snippet&key=" . config('services.youtube.api_key');

        $response = Http::get($url);

        if ($response->successful() && isset($response['items'][0])) {
            return [
                'title' => $response['items'][0]['snippet']['title'],
                'description' => $response['items'][0]['snippet']['description']
            ];
        }

        return null;
    }

    public function searchChannels($query)
    {
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=" . urlencode($query) . "&key=" . $this->apiKey;

        $response = Http::get($url);
        if ($response->failed()) {
            return [];
        }

        $channels = [];
        foreach ($response->json()['items'] as $item) {
            $channels[] = [
                'id' => $item['id']['channelId'],
                'name' => $item['snippet']['title']
            ];
        }

        return $channels;
    }

    public function fetchChannelVideos($channelId)
    {
        $videos = [];
        $videoIds = [];
        $pageToken = null;

        do {
            // Fetch videos from the channel (50 per request)
            $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=50&channelId=" . urlencode($channelId) . "&key=" . $this->apiKey;

            if ($pageToken) {
                $url .= "&pageToken={$pageToken}";
            }

            $response = Http::timeout(10)->get($url); // Set timeout for each request
            if ($response->failed()) {
                return $videos; // Return what we have if the request fails
            }

            $data = $response->json();
            if (!isset($data['items']) || empty($data['items'])) {
                break; // Stop if no items are returned
            }

            // Collect video IDs
            foreach ($data['items'] as $item) {
                if (isset($item['id']['videoId'])) {
                    $videoIds[] = $item['id']['videoId'];
                }
            }

            // Check for the next page
            $pageToken = $data['nextPageToken'] ?? null;

            // Sleep for 1 second to avoid hitting API rate limits
            sleep(1);

        } while ($pageToken);

        // Process video IDs in batches of 50
        foreach (array_chunk($videoIds, 50) as $videoChunk) {
            $detailsUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" . implode(',', $videoChunk) . "&key=" . $this->apiKey;

            $detailsResponse = Http::timeout(30)->get($detailsUrl); // Set timeout
            if ($detailsResponse->failed()) {
                continue; // Skip failed requests but continue processing others
            }

            $videoDetails = $detailsResponse->json();
            if (!isset($videoDetails['items'])) {
                continue;
            }

            foreach ($videoDetails['items'] as $video) {
                $videos[] = [
                    'id'          => $video['id'],
                    'title'       => $video['snippet']['title'],
                    'description' => $video['snippet']['description'],
                    'thumbnail'   => $video['snippet']['thumbnails']['high']['url'],
                ];
            }

            // Sleep for 1 second between batch requests to prevent timeouts
            sleep(1);
        }

        return $videos;
    }


}