<?php include 'assets/html/header.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[80vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)]">Loading episodes...</p>
        </div>

        <div id="episode-content" class="hidden">
            <!-- Season Header Section -->
            <div class="relative w-full py-12 border-b border-[var(--border-color)] bg-[var(--dark-slate)]/50">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="w-full md:w-48 shrink-0">
                            <img id="season-poster" class="w-full rounded-xl shadow-xl border border-[var(--border-color)]" src="" alt="">
                        </div>
                        <div class="flex-1">
                            <nav class="flex text-sm text-[var(--light-text)] mb-4 font-medium">
                                <a href="index.php" class="hover:text-[var(--accent-red)]">Home</a>
                                <span class="mx-2">/</span>
                                <span id="breadcrumb-anime" class="text-[var(--text-color)]"></span>
                            </nav>
                            <h2 id="season-name" class="text-3xl md:text-4xl font-black mb-2"></h2>
                            <div class="flex flex-wrap gap-4 text-sm font-bold mb-4">
                                <span id="season-rating" class="text-yellow-400 flex items-center"><i class="fas fa-star mr-1"></i></span>
                                <span id="season-ep-count" class="text-[var(--accent-red)]"></span>
                                <span id="season-duration" class="text-[var(--light-text)]"></span>
                            </div>
                            <p id="season-description" class="text-[var(--light-text)] text-sm md:text-base max-w-4xl line-clamp-3 md:line-clamp-none"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episodes List -->
            <div class="container mx-auto px-4 py-12">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold flex items-center">
                        <span class="w-1 h-8 bg-[var(--primary-red)] rounded-full mr-3"></span>
                        Episodes
                    </h3>
                </div>
                
                <div id="episodes-container" class="space-y-4">
                    <!-- Episode items will be injected here -->
                </div>
            </div>
        </div>
    </main>

    <?php include 'assets/html/footer.php'; ?>

    <script>
        // Get season ID from URL: episodes.php?id/xyz
        const seasonId = window.location.search.substring(1);

        async function fetchEpisodes() {
            if (!seasonId) {
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">No season selected.</p>';
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
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">Failed to load episodes. Please try again.</p>';
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
                epDiv.className = "episode-card bg-[var(--dark-slate)]/30 border border-[var(--border-color)] rounded-2xl overflow-hidden flex flex-col md:flex-row";
                
                epDiv.innerHTML = `
                    <div class="md:w-64 shrink-0 relative aspect-video md:aspect-auto">
                        <img src="${ep.image}" class="w-full h-full object-cover" alt="${ep.title}">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition"></div>
                        <div class="absolute bottom-2 left-2 bg-[var(--primary-red)] text-[10px] font-bold px-2 py-0.5 rounded">
                            ${ep.episodeNumber}
                        </div>
                    </div>
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-lg font-bold text-white">${ep.title}</h4>
                                <span class="text-xs text-[var(--light-text)] font-medium">${ep.airDate}</span>
                            </div>
                            <p class="text-[var(--light-text)] text-sm line-clamp-2 md:line-clamp-3 mb-4">
                                ${ep.overview || 'No description available for this episode.'}
                            </p>
                        </div>
                        <div class="flex justify-end">
                            <a href="watch.php?series=${ep.episodeId}" class="bg-[var(--primary-red)] hover:bg-[var(--accent-red)] text-white px-6 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
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
