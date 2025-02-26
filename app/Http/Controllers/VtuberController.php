<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vtuber;

class VtuberController extends Controller
{
    public function index()
    {
        $vtubers = Vtuber::all();
        return view('dashboard.vtubers.index', compact('vtubers'));
    }
}