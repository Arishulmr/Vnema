<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vnema</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-blue-600">Vnema</a>
            <div class="flex gap-4">
                <button onclick="toggleFilter('vtuber-filter')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    VTuber Filter
                </button>
                <button onclick="toggleFilter('channel-filter')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Channel Filter
                </button>
            </div>
        </div>
    </nav>

    <!-- VTuber Filter Section -->
    <div id="vtuber-filter" class="bg-white shadow-md p-4 mt-4 hidden">
        <div class="max-w-6xl mx-auto flex flex-wrap gap-2">
            <a href="/" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">All VTubers</a>
            @foreach ($vtubers as $vtuber)
                <a href="?vtuber_id={{ $vtuber->id }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $vtuber->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Channel Filter Section -->
    <div id="channel-filter" class="bg-white shadow-md p-4 mt-4 hidden">
        <div class="max-w-6xl mx-auto flex flex-wrap gap-2">
            <a href="/" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">All Channels</a>
            @foreach ($channels as $channel)
                <a href="?youtube_channel_id={{ $channel->id }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $channel->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Latest Clips Section -->
    <section class="max-w-full mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Latest VTuber Clips</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($videos as $video)
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <a href="https://www.youtube.com/watch?v={{ $video->youtube_video_id }}" target="_blank">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="rounded-md">
                    </a>
                    <h3 class="mt-2 font-semibold text-gray-800">{{ $video->title }}</h3>
                </div>
            @endforeach
        </div>
        <div class="mt-6 text-center">
            <a class="text-blue-600 font-semibold hover:underline">{{ $videos->links() }}</a>
        </div>
    </section>



    <!-- JavaScript for Toggle Functionality -->
    <script>
        function toggleFilter(filterId) {
            let filters = ['vtuber-filter', 'channel-filter'];
            filters.forEach(id => {
                if (id === filterId) {
                    document.getElementById(id).classList.toggle('hidden');
                } else {
                    document.getElementById(id).classList.add('hidden');
                }
            });
        }
    </script>

</body>
</html>
