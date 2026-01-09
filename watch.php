<?php include 'assets/html/header.php'; ?>

    <main id="main-content" class="container mx-auto px-4 py-6">
        <!-- Loading -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)]">Preparing your stream...</p>
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
                        <a id="prev-btn" href="#" class="flex-1 bg-[var(--dark-slate)] hover:bg-[var(--border-color)] p-3 rounded-xl flex items-center justify-center gap-2 transition disabled:opacity-50">
                            <i class="fas fa-step-backward"></i>
                            <span class="font-bold text-sm">Prev</span>
                        </a>
                        <div class="flex-1 bg-[var(--primary-red)] p-3 rounded-xl flex items-center justify-center gap-2">
                            <span id="current-ep-label" class="font-black text-sm uppercase">Loading...</span>
                        </div>
                        <a id="next-btn" href="#" class="flex-1 bg-[var(--dark-slate)] hover:bg-[var(--border-color)] p-3 rounded-xl flex items-center justify-center gap-2 transition">
                            <span class="font-bold text-sm">Next</span>
                            <i class="fas fa-step-forward"></i>
                        </a>
                    </div>

                    <!-- Episode Info -->
                    <div class="bg-[var(--dark-bg)]/50 p-6 rounded-2xl border border-[var(--border-color)]">
                        <h2 id="ep-title" class="text-2xl font-bold mb-2"></h2>
                        <div class="flex items-center gap-4 text-[var(--light-text)] text-xs font-bold mb-4 uppercase tracking-widest">
                            <span id="anime-title" class="text-[var(--accent-red)]"></span>
                            <span class="w-1 h-1 bg-[var(--border-color)] rounded-full"></span>
                            <span id="season-name"></span>
                        </div>
                        <p id="ep-overview" class="text-[var(--light-text)] text-sm leading-relaxed"></p>
                    </div>
                </div>

                <!-- Right: Quick List -->
                <div class="space-y-4">
                    <h3 class="text-xl font-bold flex items-center px-2">
                        <span class="w-1 h-6 bg-[var(--primary-red)] rounded-full mr-3"></span>
                        Up Next
                    </h3>
                    <div id="episode-list" class="space-y-3 max-h-[800px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Episode items injected here -->
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include 'assets/html/footer.php'; ?>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const episodeId = urlParams.get('series');

        async function fetchStreamData() {
            if (!episodeId) {
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">No episode selected.</p>';
                return;
            }

            try {
                const response = await fetch(`/api/anime-world-india/v1/stream.php?episodeId=${episodeId}`);
                const data = await response.json();

                if (data.success) {
                    renderWatchPage(data);
                } else {
                    throw new Error('Streaming data unavailable');
                }
            } catch (err) {
                document.getElementById('loading').innerHTML = '<p class="text-[var(--accent-red)]">Failed to load video. Please refresh.</p>';
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
                link.className = `flex gap-3 p-3 rounded-xl border border-[var(--border-color)] transition group hover:bg-[var(--dark-slate)] ${isActive ? 'ep-active' : 'bg-[var(--dark-bg)]/30'}`;
                
                link.innerHTML = `
                    <div class="w-24 h-14 shrink-0 rounded-lg overflow-hidden relative">
                        <img src="${ep.image}" class="w-full h-full object-cover" alt="">
                        ${isActive ? '<div class="absolute inset-0 bg-[var(--primary-red)]/40 flex items-center justify-center"><i class="fas fa-play text-xs"></i></div>' : ''}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <h4 class="text-xs font-bold text-[var(--light-text)] uppercase tracking-tighter mb-1">${ep.episodeNumber}</h4>
                        <p class="text-sm font-semibold truncate group-hover:text-[var(--accent-red)] transition">${ep.title}</p>
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
