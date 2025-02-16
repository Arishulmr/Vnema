<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class YouTubeProvider extends ServiceProvider
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
    }

    public function searchClips($keywords, $channels)
    {
        $query = implode('|', $keywords);
        $channelFilter = implode('|', $channels);

        $url = "https://www.googleapis.com/youtube/v3/search";

        $response = Http::get($url, [
            'part' => 'snippet',
            'q' => $query,
            'channelId' => $channelFilter,
            'type' => 'video',
            'maxResults' => 10,
            'key' => $this->apiKey
        ]);

        return $response->json();
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

    // Fetch all videos from a channel
    public function fetchChannelVideos($channelId)
{
    $videos = [];
    $pageToken = null;

    do {
        // Build URL with pagination support
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=50&channelId={$channelId}&key={$this->apiKey}";
        if ($pageToken) {
            $url .= "&pageToken={$pageToken}";
        }

        // Fetch data from YouTube API
        $response = Http::get($url);
        if ($response->failed()) {
            return $videos; // Return what we have if the request fails
        }

        $data = $response->json();
        foreach ($data['items'] as $item) {
            $videos[] = [
                'id' => $item['id']['videoId'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'thumbnail' => $item['snippet']['thumbnails']['high']['url']
            ];
        }

        // Check if there is another page
        $pageToken = $data['nextPageToken'] ?? null;

    } while ($pageToken); // Continue fetching while there is a next page

    return $videos;
}

}