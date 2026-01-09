<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Movie - Anime World India</title>
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
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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
        .movie-card:hover img {
            transform: scale(1.05);
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

    <main id="main-content" class="container mx-auto px-4 py-8">
        <!-- Loading -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-700 h-12 w-12 mb-4"></div>
            <p class="text-slate-400">Loading your movie...</p>
        </div>

        <div id="movie-content" class="hidden space-y-10">
            <!-- Player Section -->
            <div class="max-w-6xl mx-auto">
                <div class="video-container mb-8">
                    <iframe id="player-frame" src="" allowfullscreen></iframe>
                </div>

                <!-- Info Block -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Poster (Hidden on mobile for better flow) -->
                    <div class="hidden lg:block">
                        <img id="movie-poster" class="w-full rounded-2xl border border-slate-800 shadow-xl" src="" alt="">
                    </div>
                    
                    <!-- Text Details -->
                    <div class="lg:col-span-3 space-y-4">
                        <div class="flex flex-wrap gap-3">
                            <span id="movie-year" class="bg-slate-800 px-3 py-1 rounded-md text-xs font-bold text-slate-300"></span>
                            <span id="movie-duration" class="bg-slate-800 px-3 py-1 rounded-md text-xs font-bold text-slate-300"></span>
                            <span id="movie-rating" class="bg-yellow-500/20 text-yellow-500 border border-yellow-500/30 px-3 py-1 rounded-md text-xs font-bold"></span>
                        </div>
                        <h2 id="movie-title" class="text-3xl md:text-5xl font-black"></h2>
                        <p id="movie-description" class="text-slate-400 leading-relaxed text-lg"></p>
                    </div>
                </div>
            </div>

            <!-- More Movies Section -->
            <div class="pt-8">
                <h3 class="text-2xl font-bold mb-8 flex items-center">
                    <span class="w-1 h-8 bg-blue-500 rounded-full mr-4"></span>
                    More Movies for You
                </h3>
                <div id="more-movies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <!-- Recommendations injected here -->
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-800 mt-12 py-8 text-center text-slate-500 text-sm">
        <p>© 2024 Anime World India. All rights reserved.</p>
    </footer>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        // Supports movie.php?id-slug or movie.php?id=id-slug
        const movieId = window.location.search.substring(1) || urlParams.get('id');

        async function initPage() {
            if (!movieId) {
                document.getElementById('loading').innerHTML = '<p class="text-red-500 text-xl font-bold">Error: No Movie ID specified.</p>';
                return;
            }

            try {
                // Fetch Movie & Stream Data
                const streamRes = await fetch(`https://anime-world.xo.je/api/anime-world-india/v1/stream.php?movieId=${movieId}`);
                const streamData = await streamRes.json();

                if (!streamData.success) throw new Error('Movie not found');

                renderMovie(streamData);
                fetchRecommendations();

            } catch (err) {
                document.getElementById('loading').innerHTML = `<p class="text-red-500">Failed to load content. Please try again later.</p>`;
            }
        }

        function renderMovie(data) {
            const { movie, stream } = data;
            
            document.title = `Watch ${movie.title} - Anime World`;
            document.getElementById('player-frame').src = stream.streamLink;
            document.getElementById('movie-poster').src = movie.poster;
            document.getElementById('movie-title').textContent = movie.title;
            document.getElementById('movie-description').textContent = movie.description;
            document.getElementById('movie-year').textContent = movie.year.split('-')[0];
            document.getElementById('movie-duration').textContent = movie.duration;
            document.getElementById('movie-rating').innerHTML = `<i class="fas fa-star mr-1"></i> ${movie.rating}`;

            document.getElementById('loading').classList.add('hidden');
            document.getElementById('movie-content').classList.remove('hidden');
        }

        async function fetchRecommendations() {
            try {
                const res = await fetch(`https://anime-world.xo.je/api/anime-world-india/v1/movie.php?p=1`);
                const data = await res.json();
                
                if (data.success) {
                    const grid = document.getElementById('more-movies-grid');
                    // Filter out current movie and show first 6
                    const filtered = data.movies.filter(m => m.movieId !== movieId).slice(0, 6);
                    
                    filtered.forEach(m => {
                        const card = document.createElement('a');
                        card.href = `movie.php?${m.movieId}`;
                        card.className = "movie-card group space-y-3";
                        
                        card.innerHTML = `
                            <div class="relative aspect-[2/3] overflow-hidden rounded-xl bg-slate-800">
                                <img src="${m.image}" class="w-full h-full object-cover transition-transform duration-500" alt="${m.title}">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg transform scale-75 group-hover:scale-100 transition-transform">
                                        <i class="fas fa-play text-white"></i>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-sm px-2 py-1 rounded text-[10px] font-bold text-yellow-500 border border-yellow-500/20">
                                    <i class="fas fa-star text-[8px] mr-1"></i> ${m.rating.split(' ')[1] || m.rating}
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm truncate group-hover:text-blue-400 transition">${m.title}</h4>
                                <p class="text-xs text-slate-500">${m.year.split('-')[0]}</p>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                }
            } catch (e) {
                console.error("Recommendations failed to load");
            }
        }

        document.addEventListener('DOMContentLoaded', initPage);
    </script>
</body>
</html>