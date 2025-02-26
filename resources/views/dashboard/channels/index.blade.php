@extends('layouts.dashboard')
@section('main')




        <h1 class="text-2xl font-bold mb-4">YouTube Channel Dashboard</h1>

        <!-- Search Form -->
        <div class="mb-4">
            <input type="text" id="searchQuery" placeholder="Search for a YouTube channel..."
                   class="w-full p-2 border rounded">
            <button onclick="searchChannels()" class="mt-2 bg-blue-500 text-white p-2 rounded">Search</button>
        </div>

        <!-- Search Results -->
        <div id="searchResults" class="mt-4"></div>

        <!-- Added Channels -->
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Channel Name</th>
                    <th class="border border-gray-300 px-4 py-2">YouTube Channel ID</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($channels as $channel)
                    <tr class="text-center">
                        <td class="border border-gray-300 px-4 py-2">{{ $channel->name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $channel->youtube_channel_id }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form action="{{ route('channels.destroy', $channel->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this channel and its videos?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

 




    <script>
        function searchChannels() {
            const query = document.getElementById('searchQuery').value;
            axios.post('/channels/search', { query })
                .then(response => {
                    let resultsDiv = document.getElementById('searchResults');
                    resultsDiv.innerHTML = '';
                    response.data.forEach(channel => {
                        resultsDiv.innerHTML += `
                            <div class="p-2 border rounded flex justify-between">
                                <span>${channel.name}</span>
                                <form action="/channels/add" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="youtube_channel_id" value="${channel.id}">
                                    <input type="hidden" name="name" value="${channel.name}">
                                    <button type="submit" class="bg-green-500 text-white p-1 rounded">Add</button>
                                </form>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error(error));
        }
    </script>

@endsection
