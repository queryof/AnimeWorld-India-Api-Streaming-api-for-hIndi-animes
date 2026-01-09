<?php include 'assets/html/header.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        
        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)] animate-pulse">Fetching latest anime...</p>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden flex flex-col items-center justify-center min-h-[60vh] text-center">
            <i class="fas fa-exclamation-circle text-[var(--primary-red)] text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold">Oops! Something went wrong</h2>
            <p class="text-[var(--light-text)] mt-2">Could not connect to the API. Please try again later.</p>
            <button onclick="fetchAnimeData()" class="mt-6 bg-[var(--dark-slate)] hover:bg-[var(--border-color)] px-6 py-2 rounded-lg transition">Retry</button>
        </div>

        <!-- Content Area -->
        <div id="content" class="hidden space-y-12">
            
            <!-- Latest Series Section -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold flex items-center">
                        <span class="w-1 h-8 bg-[var(--primary-red)] rounded-full mr-3"></span>
                        Latest Series
                    </h2>
                    <a href="/allseries.php" class="text-[var(--accent-red)] text-sm hover:underline">View All</a>
                </div>
                <div id="series-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <!-- Cards will be injected here -->
                </div>
            </section>

            <!-- Latest Movies Section -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold flex items-center">
                        <span class="w-1 h-8 bg-purple-500 rounded-full mr-3"></span> <!-- Consider changing to a reddish tone for consistency -->
                        Latest Movies
                    </h2>
                    <a href="/allmovie.php" class="text-[var(--accent-red)] text-sm hover:underline">View All</a>
                </div>
                <div id="movies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <!-- Cards will be injected here -->
                </div>
            </section>

        </div>
    </main>

    <?php include 'assets/html/footer.php'; ?>

    <script>
        const HOME_API_URL = '/api/anime-world-india/v1/home.php';

        async function fetchAnimeData() {
            const loadingEl = document.getElementById('loading');
            const contentEl = document.getElementById('content');
            const errorEl = document.getElementById('error');

            loadingEl.classList.remove('hidden');
            contentEl.classList.add('hidden');
            errorEl.classList.add('hidden');

            try {
                const response = await fetch(HOME_API_URL);
                const data = await response.json();

                if (data.success) {
                    renderGrid('series-grid', data.latest_series, 'series');
                    renderGrid('movies-grid', data.latest_movies, 'movie');
                    
                    loadingEl.classList.add('hidden');
                    contentEl.classList.remove('hidden');
                } else {
                    throw new Error('API reported failure');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                loadingEl.classList.add('hidden');
                errorEl.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchAnimeData();
        });
    </script>
