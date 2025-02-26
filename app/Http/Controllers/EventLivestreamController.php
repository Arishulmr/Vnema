<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Livestream;
use Illuminate\Http\Request;

class EventLivestreamController extends Controller
{
    public function index()
    {
        $events = Event::paginate(5);
        $livestreams = Livestream::whereNull('event_id')->paginate(5);

        return view('dashboard.events.assign_livestreams', compact ('events', 'livestreams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'livestream_id' => 'required|exists:livestreams,id',
        ]);

        Livestream::where('id', $request->livestream_id)->update(['event_id' => $request->event_id]);

        return redirect()->route('dashboard.assign_livestreams')->with('success', 'Livestream assigned successfully!');
    }
}
