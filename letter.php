<?php include 'assets/html/header.php'; ?>

<main class="container mx-auto px-4 py-8">
    <section>
        <h2 id="letter-display" class="text-3xl font-bold mb-8">
            Anime starting with "<span class="text-[var(--primary-red)]"></span>"
        </h2>

        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)] animate-pulse">Loading anime...</p>
        </div>

        <!-- No Results State -->
        <div id="no-results" class="hidden flex-col items-center justify-center min-h-[60vh] text-center">
            <i class="fas fa-sad-tear text-[var(--light-text)] text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold">No anime found</h2>
            <p class="text-[var(--light-text)] mt-2">Could not retrieve any anime starting with this letter.</p>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden flex-col items-center justify-center min-h-[60vh] text-center">
            <i class="fas fa-exclamation-circle text-[var(--primary-red)] text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold">Oops! Something went wrong</h2>
            <p class="text-[var(--light-text)] mt-2">Failed to load anime data. Please try again later.</p>
            <button onclick="fetchLetterContent()" class="mt-6 bg-[var(--dark-slate)] hover:bg-[var(--border-color)] px-6 py-2 rounded-lg transition">Retry</button>
        </div>

        <!-- Anime Grid -->
        <div id="anime-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <!-- Anime items will be injected here -->
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center space-x-4 mt-12 hidden">
            <button id="prev-page" class="bg-[var(--dark-slate)] hover:bg-[var(--primary-red)] px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:hover:bg-[var(--dark-slate)]" disabled>
                <i class="fas fa-chevron-left mr-2"></i> Prev
            </button>
            <span id="page-info" class="text-[var(--light-text)] text-sm">Page 1 of 1</span>
            <button id="next-page" class="bg-[var(--dark-slate)] hover:bg-[var(--primary-red)] px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:hover:bg-[var(--dark-slate)]" disabled>
                Next <i class="fas fa-chevron-right ml-2"></i>
            </button>
        </div>
    </section>
</main>

<?php include 'assets/html/footer.php'; ?>

<script>
    const A2Z_API_URL = '/api/anime-world-india/v1/a2z.php';
    let currentLetter = '';
    let currentPage = 1;
    let totalPages = 1;

    async function fetchLetterContent() {
        const urlParams = new URLSearchParams(window.location.search);
        currentLetter = urlParams.get('letter') || '';
        currentPage = parseInt(urlParams.get('page')) || 1;

        const letterDisplay = document.getElementById('letter-display');
        const loadingEl = document.getElementById('loading');
        const noResultsEl = document.getElementById('no-results');
        const errorEl = document.getElementById('error');
        const animeGrid = document.getElementById('anime-grid');
        const paginationEl = document.getElementById('pagination');
        const pageInfoEl = document.getElementById('page-info');
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');

        letterDisplay.querySelector('span').textContent = currentLetter.toUpperCase();
        loadingEl.classList.remove('hidden');
        noResultsEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        animeGrid.innerHTML = '';
        paginationEl.classList.add('hidden');

        if (!currentLetter) {
            loadingEl.classList.add('hidden');
            noResultsEl.classList.remove('hidden');
            noResultsEl.innerHTML = '<i class="fas fa-exclamation-circle text-[var(--light-text)] text-5xl mb-4"></i><h2 class="text-2xl font-bold">No letter specified.</h2><p class="text-[var(--light-text)] mt-2">Please select a letter from the A-Z navigation.</p>';
            return;
        }

        try {
            const response = await fetch(`${A2Z_API_URL}?letter=${encodeURIComponent(currentLetter)}&page=${currentPage}`);
            const data = await response.json();

            loadingEl.classList.add('hidden');

            if (data.success && data.results && data.results.length > 0) {
                // The API returns 'poster' but renderGrid expects 'image'
                const items = data.results.map(item => ({
                    ...item, 
                    image: item.poster, 
                    movieId: item.type === 'movie' ? item.id.replace('movie/','') : null,
                    seriesId: item.type === 'series' ? item.id.replace('series/','') : null
                }));
                renderGrid('anime-grid', items, null, false);
                totalPages = data.total_pages || 1;
                pageInfoEl.textContent = `Page ${currentPage} of ${totalPages}`;
                
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
                paginationEl.classList.remove('hidden');
            } else {
                noResultsEl.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            errorEl.classList.remove('hidden');
        }
    }

    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            window.location.href = `letter.php?letter=${encodeURIComponent(currentLetter)}&page=${currentPage - 1}`;
        }
    });

    document.getElementById('next-page').addEventListener('click', () => {
        if (currentPage < totalPages) {
            window.location.href = `letter.php?letter=${encodeURIComponent(currentLetter)}&page=${currentPage + 1}`;
        }
    });

    document.addEventListener('DOMContentLoaded', fetchLetterContent);

    // renderGrid is globally available from footer.php
</script>

