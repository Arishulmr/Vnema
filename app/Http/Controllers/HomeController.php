<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Vtuber;


class HomeController extends Controller
{

    public function index(Request $request)
    {
        $vtuberId = $request->query('vtuber_id');
        $channelId = $request->query('youtube_channel_id');
        $searchQuery = $request->query('query'); // Search input
        $vtubers = Vtuber::all();
        $channels = Channel::all();

        if ($searchQuery) {
            // Extract the last 11 characters from the input (ignoring spaces)
            $livestreamId = substr(preg_replace('/\s+/', '', $searchQuery), -11);

            // Fetch videos related to the found livestream
            $videos = Video::whereHas('livestreams', function ($query) use ($livestreamId) {
                $query->where('youtube_livestream_id', $livestreamId);
            })->paginate(12);
        } elseif ($vtuberId) {
            $videos = Video::whereHas('vtubers', function ($query) use ($vtuberId) {
                $query->where('vtuber_id', $vtuberId);
            })->paginate(12);
        } elseif ($channelId) {
            $videos = Video::where('channel_id', $channelId)->paginate(12);
        } else {
            $videos = Video::paginate(12);
        }

        return view('home', compact('videos', 'vtubers', 'channels'));
    }


}