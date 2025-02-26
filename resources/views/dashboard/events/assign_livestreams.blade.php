@extends('layouts.dashboard')
@section('main')

<h2 class="text-2xl font-bold mb-4">Assign Livestream to Event</h2>

@if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-2 gap-4 ">
    <div class="relative overflow-x-auto rounded-md">
        <table class="w-full text-sm text-left rtl:text-right" >
            <thead class="text-sm text-gray-700 uppercase bg-white">
                <tr class="bg-white border-t border-b">
                    <th class="px-6 py-2 text-center">ID</th>
                    <th class="px-6 py-2 text-center">Event Name</th>
                    <th class="px-6 py-2 text-center">End Time</th>
                    <th class="px-6 py-2 text-center">Description</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($events as $event)
                    <tr class="bg-white border-b">
                    <td class="px-6 py-2 text-center">{{$event->id}}</td>
                    <td class="px-6 py-2 text-center">{{$event->name}}</td>
                    <td class="px-6 py-2 text-center">{{$event->end_time}}</td>
                    <td class="px-6 py-2 text-center">{{$event->description}}</td>
                    </tr>
                    @endforeach
            </tbody>

        </table>
    {{ $events->links() }}

    </div>

    <div class="relative overflow-x-auto rounded-md">
        <table class="w-full text-sm text-left rtl:text-right">
            <thead class="text-sm text-gray-700 uppercase bg-white">
                <tr class="bg-white border-t border-b">
                    <th class="px-6 py-2 text-center">ID</th>
                    <th class="px-6 py-2 text-center">Title</th>
                    <th class="px-6 py-2 text-center">Description</th>
                    <th class="px-6 py-2 text-center">Event</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($livestreams as $stream)
                <tr>
                    <td class="px-6 py-2 text-center">{{$stream->youtube_livestream_id}}</td>
                    <td class="px-6 py-2 text-center">{{$stream->title}}</td>
                    <td class="px-6 py-2 text-center">{{$stream->description}}</td>
                    <td class="px-6 py-2 text-center">{{$stream->event_id}}</td>
                </tr>
                    @endforeach
            </tbody>

        </table>
        {{ $livestreams->links() }}

    </div>
    <div class="border border-gray-300 p-4 rounded-lg">
        <form action="{{ route('events.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="event_name">Event name:</label>
                    <input type="text" name="event_name" class="border border-grey-100 rounded-md p-2 w-full">
                </div>
                <div>
                    <label for="start_date">Start Date:</label>
                    <input class="border border-grey-100 rounded-md p-2 w-full" type="date" name="start_date">
                </div>
                <div>
                    <label for="description">Description:</label>
                    <input class="border border-grey-100 rounded-md p-2 w-full" type="text" name="description">
                </div>
                <div>
                    <label for="start_date">End Date:</label>
                    <input class="border border-grey-100 rounded-md p-2 w-full" type="date" name="end_date">
                </div>
                <button type="submit" class="col-span-2 bg-blue-500 text-white px-4 py-2 mt-4">Create</button>
            </div>
        </form>
    </div>

    <div class="border border-gray-300 p-4 rounded-lg">
        <form action="{{ route('dashboard.assign_livestreams.store') }}" method="POST">
            @csrf
            <div class="grid grid-rows-3">
            <div class=""">
                <label for="event">Select Event:</label>
                <select name="event_id" required>
                    @foreach($events as $event)
                        <option class="p-4" value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
                <div class="mt-4">
                    <label for="livestream">Select Livestream:</label>
                    <select name="livestream_id" required>
                        @foreach($livestreams as $livestream)
                            <option value="{{ $livestream->id }}">{{ $livestream->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
                <button type="submit" class=" bg-blue-500 text-white px-4 py-2 mt-4">Assign</button>
            </form>
        </div>


    </div>

@endsection
