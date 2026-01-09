<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series Details - Anime World India</title>
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
        .hero-gradient {
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.4), #0f172a);
        }
        .season-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
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
                <a href="/allseries.php" class="hover:text-blue-400 transition text-blue-400">Series</a>
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
            <p class="text-slate-400">Loading series information...</p>
        </div>

        <!-- Details Section -->
        <div id="series-content" class="hidden">
            <!-- Hero Section -->
            <div class="relative w-full min-h-[500px] flex items-end">
                <div id="hero-bg" class="absolute inset-0 bg-cover bg-center"></div>
                <div class="absolute inset-0 hero-gradient"></div>
                
                <div class="container mx-auto px-4 py-12 relative z-10">
                    <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
                        <img id="series-poster" class="w-64 rounded-2xl shadow-2xl border-4 border-slate-800/50" src="" alt="">
                        <div class="flex-1 space-y-4">
                            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                                <span class="bg-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Series</span>
                                <span id="series-year" class="bg-slate-800 px-3 py-1 rounded-full text-xs font-bold"></span>
                                <span id="series-rating" class="bg-yellow-500/20 text-yellow-500 border border-yellow-500/30 px-3 py-1 rounded-full text-xs font-bold"></span>
                            </div>
                            <h2 id="series-title" class="text-4xl md:text-6xl font-black"></h2>
                            <p id="series-description" class="text-slate-300 max-w-3xl leading-relaxed text-lg"></p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-6 text-slate-400 text-sm font-medium pt-4">
                                <div><i class="far fa-clock mr-2 text-blue-500"></i><span id="series-duration"></span></div>
                                <div><i class="fas fa-layer-group mr-2 text-blue-500"></i><span id="series-total-seasons"></span> Seasons</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seasons Grid -->
            <div class="container mx-auto px-4 py-12">
                <h3 class="text-2xl font-bold mb-8 flex items-center">
                    <span class="w-1 h-8 bg-blue-500 rounded-full mr-3"></span>
                    Available Seasons
                </h3>
                <div id="seasons-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Season cards will be injected here -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-12 py-8 bg-slate-900">
        <div class="container mx-auto px-4 text-center">
            <p class="text-slate-500 text-sm">© 2024 Anime World India. Premium Anime Streaming.</p>
        </div>
    </footer>

    <script>
        // Get series ID from URL: series.php?id=xyz
        const urlParams = new URLSearchParams(window.location.search);
        // Note: supporting both ?[id] and ?seriesID=[id] patterns
        const seriesId = window.location.search.substring(1) || urlParams.get('seriesID');

        async function fetchSeriesDetails() {
            if (!seriesId) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">No series ID provided.</p>';
                return;
            }

            try {
                const response = await fetch(`/api/anime-world-india/v1/seasons.php?seriesID=${seriesId}`);
                const data = await response.json();

                if (data.success) {
                    renderSeries(data.series, data.seasons);
                } else {
                    throw new Error('Series not found');
                }
            } catch (err) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500">Failed to load series data.</p>';
            }
        }

        function renderSeries(series, seasons) {
            document.title = `${series.title} - Anime World`;
            
            // Hero info
            document.getElementById('hero-bg').style.backgroundImage = `url(${series.poster})`;
            document.getElementById('series-poster').src = series.poster;
            document.getElementById('series-title').textContent = series.title;
            document.getElementById('series-description').textContent = series.description;
            document.getElementById('series-year').textContent = series.year.split('-')[0];
            document.getElementById('series-rating').innerHTML = `<i class="fas fa-star mr-1"></i> ${series.rating}`;
            document.getElementById('series-duration').textContent = series.duration;
            document.getElementById('series-total-seasons').textContent = series.totalSeasons;

            // Seasons grid
            const grid = document.getElementById('seasons-grid');
            grid.innerHTML = '';

            seasons.forEach(season => {
                const card = document.createElement('a');
                card.href = `/episodes.php?${season.seasonId}`;
                card.className = "season-card bg-slate-800/40 border border-slate-700 p-6 rounded-2xl transition-all duration-300 flex items-center justify-between group";
                
                card.innerHTML = `
                    <div class="space-y-1">
                        <span class="text-blue-500 text-xs font-bold uppercase tracking-widest">${season.seasonNumber}</span>
                        <h4 class="text-xl font-bold group-hover:text-blue-400 transition">${season.seasonName}</h4>
                        <p class="text-slate-500 text-sm">${season.episodes} Episodes</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                `;
                grid.appendChild(card);
            });

            // Switch visibility
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('series-content').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', fetchSeriesDetails);
    </script>
</body>
</html>