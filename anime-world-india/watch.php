<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Anime - Anime World India</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            background: #000;
            border-radius: 1rem;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .loader {
            border-top-color: #3b82f6;
            animation: spinner 1.5s linear infinite;
        }
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .ep-active {
            border-color: #3b82f6 !important;
            background: rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="custom-scrollbar">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-slate-800">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.html" class="flex items-center space-x-2">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i class="fas fa-play text-white"></i>
                </div>
                <h1 class="text-xl font-bold tracking-tight">ANIME<span class="text-blue-500">WORLD</span></h1>
            </a>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-slate-400 hover:text-white transition"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </header>

    <main id="main-content" class="container mx-auto px-4 py-6">
        <!-- Loading -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-700 h-12 w-12 mb-4"></div>
            <p class="text-slate-400">Preparing your stream...</p>
        </div>

        <div id="watch-content" class="hidden space-y-6">
            <!-- Player Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left: Player & Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="video-container shadow-2xl">
                        <iframe id="player-frame" src="" allowfullscreen></iframe>
                    </div>

                    <!-- Navigation Controls -->
                    <div class="flex items-center justify-between gap-4">
                        <a id="prev-btn" href="#" class="flex-1 bg-slate-800 hover:bg-slate-700 p-3 rounded-xl flex items-center justify-center gap-2 transition disabled:opacity-50">
                            <i class="fas fa-step-backward"></i>
                            <span class="font-bold text-sm">Prev</span>
                        </a>
                        <div class="flex-1 bg-blue-600 p-3 rounded-xl flex items-center justify-center gap-2">
                            <span id="current-ep-label" class="font-black text-sm uppercase">Loading...</span>
                        </div>
                        <a id="next-btn" href="#" class="flex-1 bg-slate-800 hover:bg-slate-700 p-3 rounded-xl flex items-center justify-center gap-2 transition">
                            <span class="font-bold text-sm">Next</span>
                            <i class="fas fa-step-forward"></i>
                        </a>
                    </div>

                    <!-- Episode Info -->
                    <div class="bg-slate-900/50 p-6 rounded-2xl border border-slate-800">
                        <h2 id="ep-title" class="text-2xl font-bold mb-2"></h2>
                        <div class="flex items-center gap-4 text-slate-400 text-xs font-bold mb-4 uppercase tracking-widest">
                            <span id="anime-title" class="text-blue-400"></span>
                            <span class="w-1 h-1 bg-slate-700 rounded-full"></span>
                            <span id="season-name"></span>
                        </div>
                        <p id="ep-overview" class="text-slate-400 text-sm leading-relaxed"></p>
                    </div>
                </div>

                <!-- Right: Quick List -->
                <div class="space-y-4">
                    <h3 class="text-xl font-bold flex items-center px-2">
                        <span class="w-1 h-6 bg-blue-500 rounded-full mr-3"></span>
                        Up Next
                    </h3>
                    <div id="episode-list" class="space-y-3 max-h-[800px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Episode items injected here -->
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const episodeId = urlParams.get('series');

        async function fetchStreamData() {
            if (!episodeId) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">No episode selected.</p>';
                return;
            }

            try {
                const response = await fetch(`https://anime-world.xo.je/api/anime-world-india/v1/stream.php?episodeId=${episodeId}`);
                const data = await response.json();

                if (data.success) {
                    renderWatchPage(data);
                } else {
                    throw new Error('Streaming data unavailable');
                }
            } catch (err) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">Failed to load video. Please refresh.</p>';
            }
        }

        function renderWatchPage(data) {
            const { series, current, stream, episodes, previous, next } = data;
            
            document.title = `Watching: ${current.title} - Anime World`;

            // Set Stream
            document.getElementById('player-frame').src = stream.streamLink;

            // Set Meta Info
            document.getElementById('ep-title').textContent = current.title.replace(/\n\s+/g, ' ');
            document.getElementById('anime-title').textContent = series.title;
            document.getElementById('season-name').textContent = series.season.replace(/\n\s+/g, ' ');
            document.getElementById('ep-overview').textContent = current.overview;
            document.getElementById('current-ep-label').textContent = current.episodeId.split('x')[1] ? `Episode ${current.episodeId.split('x')[1]}` : 'Playing';

            // Navigation Buttons
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');

            if (previous) {
                prevBtn.href = `watch.php?series=${previous}`;
                prevBtn.classList.remove('opacity-30', 'pointer-events-none');
            } else {
                prevBtn.classList.add('opacity-30', 'pointer-events-none');
            }

            if (next) {
                nextBtn.href = `watch.php?series=${next}`;
                nextBtn.classList.remove('opacity-30', 'pointer-events-none');
            } else {
                nextBtn.classList.add('opacity-30', 'pointer-events-none');
            }

            // Episode List Rendering
            const listContainer = document.getElementById('episode-list');
            listContainer.innerHTML = '';

            episodes.forEach(ep => {
                const isActive = ep.episodeId === current.episodeId;
                const link = document.createElement('a');
                link.href = `watch.php?series=${ep.episodeId}`;
                link.className = `flex gap-3 p-3 rounded-xl border border-slate-800 transition group hover:bg-slate-800 ${isActive ? 'ep-active' : 'bg-slate-900/30'}`;
                
                link.innerHTML = `
                    <div class="w-24 h-14 shrink-0 rounded-lg overflow-hidden relative">
                        <img src="${ep.image}" class="w-full h-full object-cover" alt="">
                        ${isActive ? '<div class="absolute inset-0 bg-blue-600/40 flex items-center justify-center"><i class="fas fa-play text-xs"></i></div>' : ''}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">${ep.episodeNumber}</h4>
                        <p class="text-sm font-semibold truncate group-hover:text-blue-400 transition">${ep.title}</p>
                    </div>
                `;
                listContainer.appendChild(link);
                
                // Scroll the active episode into view
                if(isActive) {
                    setTimeout(() => link.scrollIntoView({ behavior: 'smooth', block: 'center' }), 500);
                }
            });

            // Switch visibility
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('watch-content').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', fetchStreamData);
    </script>
</body>
</html>