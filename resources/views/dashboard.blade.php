<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
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
        <h2 class="text-xl font-semibold mt-6">Added Channels</h2>
        <ul class="mt-2">
            @foreach ($channels as $channel)
                <li class="p-2 border-b">{{ $channel->name }}</li>
            @endforeach
        </ul>
    </div>

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

</body>
</html>
