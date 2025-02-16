<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query();

        if ($request->has('agency')) {
            $query->whereHas('channel.vtuber', function ($q) use ($request) {
                $q->where('agency', $request->agency);
            });
        }

        if ($request->has('livestream')) {
            $query->whereHas('livestream', function ($q) use ($request) {
                $q->where('youtube_livestream_id', $request->livestream);
            });
        }

        return response()->json($query->get());
    }
}