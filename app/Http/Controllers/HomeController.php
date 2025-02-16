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
    $vtubers = Vtuber::all();
    $channels = Channel::all();



    if ($vtuberId) {
        $videos = Video::where('vtuber_id', $vtuberId)->paginate(12);
    } elseif ($channelId) {
        $videos = Video::where('channel_id', $channelId)->paginate(perPage: 12);
    } else {
        $videos = Video::paginate(12);
    }

    return view('home', compact('videos', 'vtubers', 'channels'));
}

}