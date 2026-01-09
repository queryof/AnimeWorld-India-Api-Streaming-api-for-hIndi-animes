<?php include 'assets/html/header.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[80vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)]">Loading series information...</p>
        </div>

        <!-- Details Section -->
        <div id="series-content" class="hidden">
            <!-- Hero Section -->
            <div class="relative w-full min-h-[500px] flex items-end">
                <div id="hero-bg" class="absolute inset-0 bg-cover bg-center"></div>
                <div class="absolute inset-0 hero-gradient"></div>
                
                <div class="container mx-auto px-4 py-12 relative z-10">
                    <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
                        <img id="series-poster" class="w-64 rounded-2xl shadow-2xl border-4 border-[var(--border-color)]/50" src="" alt="">
                        <div class="flex-1 space-y-4">
                            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                                <span class="bg-[var(--primary-red)] px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Series</span>
                                <span id="series-year" class="bg-[var(--dark-slate)] px-3 py-1 rounded-full text-xs font-bold text-[var(--light-text)]"></span>
                                <span id="series-rating" class="bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-3 py-1 rounded-full text-xs font-bold"></span>
                            </div>
                            <h2 id="series-title" class="text-4xl md:text-6xl font-black"></h2>
                            <p id="series-description" class="text-[var(--light-text)] max-w-3xl leading-relaxed text-lg"></p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-6 text-[var(--light-text)] text-sm font-medium pt-4">
                                <div><i class="far fa-clock mr-2 text-[var(--primary-red)]"></i><span id="series-duration"></span></div>
                                <div><i class="fas fa-layer-group mr-2 text-[var(--primary-red)]"></i><span id="series-total-seasons"></span> Seasons</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seasons Grid -->
            <div class="container mx-auto px-4 py-12">
                <h3 class="text-2xl font-bold mb-8 flex items-center">
                    <span class="w-1 h-8 bg-[var(--primary-red)] rounded-full mr-3"></span>
                    Available Seasons
                </h3>
                <div id="seasons-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Season cards will be injected here -->
                </div>
            </div>
        </div>
    </main>

    <?php include 'assets/html/footer.php'; ?>

    <script>
        // Get series ID from URL: series.php?id=xyz
        const urlParams = new URLSearchParams(window.location.search);
        // Note: supporting both ?[id] and ?seriesID=[id] patterns
        const seriesId = window.location.search.substring(1) || urlParams.get('seriesID');

        async function fetchSeriesDetails() {
            if (!seriesId) {
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">No series ID provided.</p>';
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
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">Failed to load series data.</p>';
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
                card.className = "season-card bg-[var(--dark-slate)]/40 border border-[var(--border-color)] p-6 rounded-2xl transition-all duration-300 flex items-center justify-between group";
                
                card.innerHTML = `
                    <div class="space-y-1">
                        <span class="text-[var(--primary-red)] text-xs font-bold uppercase tracking-widest">${season.seasonNumber}</span>
                        <h4 class="text-xl font-bold group-hover:text-[var(--accent-red)] transition">${season.seasonName}</h4>
                        <p class="text-[var(--light-text)] text-sm">${season.episodes} Episodes</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[var(--border-color)] flex items-center justify-center text-[var(--light-text)] group-hover:bg-[var(--primary-red)] group-hover:text-white transition">
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
