<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Episodes - Anime World India</title>
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
        .loader {
            border-top-color: #3b82f6;
            animation: spinner 1.5s linear infinite;
        }
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .season-hero-gradient {
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.6), #0f172a);
        }
        .episode-card {
            transition: all 0.3s ease;
        }
        .episode-card:hover {
            background-color: rgba(30, 41, 59, 0.8);
            transform: scale(1.01);
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
            
            <nav class="hidden md:flex space-x-8 text-sm font-medium text-slate-400">
                <a href="index.html" class="hover:text-blue-400 transition">Home</a>
                <a href="/allseries.php" class="hover:text-blue-400 transition">Series</a>
                <a href="/allmovie.php" class="hover:text-blue-400 transition">Movies</a>
            </nav>

            <div class="flex items-center space-x-4">
                <button class="p-2 text-slate-400 hover:text-white transition"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[80vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-700 h-12 w-12 mb-4"></div>
            <p class="text-slate-400">Loading episodes...</p>
        </div>

        <div id="episode-content" class="hidden">
            <!-- Season Header Section -->
            <div class="relative w-full py-12 border-b border-slate-800 bg-slate-900/50">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="w-full md:w-48 shrink-0">
                            <img id="season-poster" class="w-full rounded-xl shadow-xl border border-slate-700" src="" alt="">
                        </div>
                        <div class="flex-1">
                            <nav class="flex text-sm text-slate-500 mb-4 font-medium">
                                <a href="index.html" class="hover:text-blue-400">Home</a>
                                <span class="mx-2">/</span>
                                <span id="breadcrumb-anime" class="text-slate-400"></span>
                            </nav>
                            <h2 id="season-name" class="text-3xl md:text-4xl font-black mb-2"></h2>
                            <div class="flex flex-wrap gap-4 text-sm font-bold mb-4">
                                <span id="season-rating" class="text-yellow-500 flex items-center"><i class="fas fa-star mr-1"></i></span>
                                <span id="season-ep-count" class="text-blue-400"></span>
                                <span id="season-duration" class="text-slate-400"></span>
                            </div>
                            <p id="season-description" class="text-slate-400 text-sm md:text-base max-w-4xl line-clamp-3 md:line-clamp-none"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episodes List -->
            <div class="container mx-auto px-4 py-12">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold flex items-center">
                        <span class="w-1 h-8 bg-blue-500 rounded-full mr-3"></span>
                        Episodes
                    </h3>
                </div>
                
                <div id="episodes-container" class="space-y-4">
                    <!-- Episode items will be injected here -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-12 py-8 bg-slate-900">
        <div class="container mx-auto px-4 text-center">
            <p class="text-slate-500 text-sm">© 2024 Anime World India. Quality streaming for fans.</p>
        </div>
    </footer>

    <script>
        // Get season ID from URL: episodes.php?id/xyz
        const seasonId = window.location.search.substring(1);

        async function fetchEpisodes() {
            if (!seasonId) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">No season selected.</p>';
                return;
            }

            try {
                const response = await fetch(`/api/anime-world-india/v1/episodes.php?seasonId=${seasonId}`);
                const data = await response.json();

                if (data.success) {
                    renderEpisodes(data.season, data.episodes);
                } else {
                    throw new Error('Data fetch failed');
                }
            } catch (err) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">Failed to load episodes. Please try again.</p>';
            }
        }

        function renderEpisodes(season, episodes) {
            document.title = `${season.animeTitle} - ${season.seasonName}`;
            
            // Season Info
            document.getElementById('season-poster').src = season.poster;
            document.getElementById('breadcrumb-anime').textContent = season.animeTitle;
            document.getElementById('season-name').textContent = season.seasonName.replace(/\n\s+/g, ' ');
            document.getElementById('season-rating').innerHTML += season.rating;
            document.getElementById('season-ep-count').textContent = `${season.totalEpisodes} Episodes`;
            document.getElementById('season-duration').textContent = season.duration;
            document.getElementById('season-description').textContent = season.description;

            // Episodes List
            const container = document.getElementById('episodes-container');
            container.innerHTML = '';

            episodes.forEach(ep => {
                const epDiv = document.createElement('div');
                epDiv.className = "episode-card bg-slate-800/30 border border-slate-800 rounded-2xl overflow-hidden flex flex-col md:flex-row";
                
                epDiv.innerHTML = `
                    <div class="md:w-64 shrink-0 relative aspect-video md:aspect-auto">
                        <img src="${ep.image}" class="w-full h-full object-cover" alt="${ep.title}">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition"></div>
                        <div class="absolute bottom-2 left-2 bg-blue-600 text-[10px] font-bold px-2 py-0.5 rounded">
                            ${ep.episodeNumber}
                        </div>
                    </div>
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-lg font-bold text-white">${ep.title}</h4>
                                <span class="text-xs text-slate-500 font-medium">${ep.airDate}</span>
                            </div>
                            <p class="text-slate-400 text-sm line-clamp-2 md:line-clamp-3 mb-4">
                                ${ep.overview || 'No description available for this episode.'}
                            </p>
                        </div>
                        <div class="flex justify-end">
                            <a href="watch.php?series=${ep.episodeId}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                                <i class="fas fa-play text-xs"></i>
                                Watch Now
                            </a>
                        </div>
                    </div>
                `;
                container.appendChild(epDiv);
            });

            // Show content
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('episode-content').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', fetchEpisodes);
    </script>
</body>
</html>