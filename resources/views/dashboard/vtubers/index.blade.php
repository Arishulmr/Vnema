@extends('layouts.dashboard')
@section('main')

<h1 class="text-2xl font-bold mb-4">Vtubers</h1>
<div>
    <div class="grid grid-cols-2 gap-4">
        @foreach ($vtubers as $vtuber)
            <div class="w-full p-4">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center space-x-4">
                        <a href="{{$vtuber->channelUrl}}">
                        <img class="w-12 h-12 rounded-full" src="{{ $vtuber->thumbnail }}" alt="{{ $vtuber->name }}">
                    </a>
                        <div>
                            <h3 class="text-xl font-semibold">{{ $vtuber->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $vtuber->agency }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-4">
                        <div class="flex items-center">
    </div>
</div>
</div>
</div>
@endforeach
</div>
</div>

@endsection
