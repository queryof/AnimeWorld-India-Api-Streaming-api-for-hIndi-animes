<?php include 'assets/html/header.php'; ?>

    <main id="main-content" class="container mx-auto px-4 py-8">
        <!-- Loading -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)]">Loading your movie...</p>
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
                        <img id="movie-poster" class="w-full rounded-2xl border border-[var(--border-color)] shadow-xl" src="" alt="">
                    </div>
                    
                    <!-- Text Details -->
                    <div class="lg:col-span-3 space-y-4">
                        <div class="flex flex-wrap gap-3">
                            <span id="movie-year" class="bg-[var(--dark-slate)] px-3 py-1 rounded-md text-xs font-bold text-[var(--light-text)]"></span>
                            <span id="movie-duration" class="bg-[var(--dark-slate)] px-3 py-1 rounded-md text-xs font-bold text-[var(--light-text)]"></span>
                            <span id="movie-rating" class="bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-3 py-1 rounded-md text-xs font-bold"></span>
                        </div>
                        <h2 id="movie-title" class="text-3xl md:text-5xl font-black"></h2>
                        <p id="movie-description" class="text-[var(--light-text)] leading-relaxed text-lg"></p>
                    </div>
                </div>
            </div>

            <!-- More Movies Section -->
            <div class="pt-8">
                <h3 class="text-2xl font-bold mb-8 flex items-center">
                    <span class="w-1 h-8 bg-[var(--primary-red)] rounded-full mr-4"></span>
                    More Movies for You
                </h3>
                <div id="more-movies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <!-- Recommendations injected here -->
                </div>
            </div>
        </div>
    </main>

    <?php include 'assets/html/footer.php'; ?>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        // Supports movie.php?id-slug or movie.php?id=id-slug
        const movieId = window.location.search.substring(1) || urlParams.get('id');

        async function initPage() {
            if (!movieId) {
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)] text-xl font-bold">Error: No Movie ID specified.</p>';
                return;
            }

            try {
                // Fetch Movie & Stream Data
                const streamRes = await fetch(`/api/anime-world-india/v1/stream.php?movieId=${movieId}`);
                const streamData = await streamRes.json();

                if (!streamData.success) throw new Error('Movie not found');

                renderMovie(streamData);
                fetchRecommendations();

            } catch (err) {
                document.getElementById('loading').innerHTML = `<p class="text-[var(--accent-red)]">Failed to load content. Please try again later.</p>`;
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
                const res = await fetch(`/api/anime-world-india/v1/movie.php?p=1`);
                const data = await res.json();
                
                if (data.success) {
                    const filtered = data.movies.filter(m => m.movieId !== movieId).slice(0, 6);
                    renderGrid('more-movies-grid', filtered, 'movie');
                }
            } catch (e) {
                console.error("Recommendations failed to load");
            }
        }

        document.addEventListener('DOMContentLoaded', initPage);
    </script>
