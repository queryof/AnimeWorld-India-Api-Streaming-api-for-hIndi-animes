<?php include 'assets/html/header.php'; ?>

<main class="container mx-auto px-4 py-8">
    <section>
        <h2 class="text-3xl font-bold mb-8">
            All Movies
        </h2>

        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-[var(--border-color)] h-12 w-12 mb-4"></div>
            <p class="text-[var(--light-text)] animate-pulse">Loading movies...</p>
        </div>

        <!-- No Results State -->
        <div id="no-results" class="hidden flex-col items-center justify-center min-h-[60vh] text-center">
            <i class="fas fa-sad-tear text-[var(--light-text)] text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold">No movies found</h2>
            <p class="text-[var(--light-text)] mt-2">Could not retrieve any movies at this time.</p>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden flex-col items-center justify-center min-h-[60vh] text-center">
            <i class="fas fa-exclamation-circle text-[var(--primary-red)] text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold">Oops! Something went wrong</h2>
            <p class="text-[var(--light-text)] mt-2">Failed to load movies. Please try again later.</p>
            <button onclick="fetchMovieList()" class="mt-6 bg-[var(--dark-slate)] hover:bg-[var(--border-color)] px-6 py-2 rounded-lg transition">Retry</button>
        </div>

        <!-- Movies Grid -->
        <div id="movies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <!-- Movie items will be injected here -->
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
    const MOVIES_API_URL = '/api/anime-world-india/v1/movie.php';
    let currentPage = 1;
    let totalPages = 1;

    async function fetchMovieList() {
        const urlParams = new URLSearchParams(window.location.search);
        currentPage = parseInt(urlParams.get('p')) || 1;

        const loadingEl = document.getElementById('loading');
        const noResultsEl = document.getElementById('no-results');
        const errorEl = document.getElementById('error');
        const moviesGrid = document.getElementById('movies-grid');
        const paginationEl = document.getElementById('pagination');
        const pageInfoEl = document.getElementById('page-info');
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');

        loadingEl.classList.remove('hidden');
        noResultsEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        moviesGrid.innerHTML = '';
        paginationEl.classList.add('hidden');

        try {
            const response = await fetch(`${MOVIES_API_URL}?p=${currentPage}`);
            const data = await response.json();

            loadingEl.classList.add('hidden');

            if (data.success && data.movies && data.movies.length > 0) {
                renderGrid('movies-grid', data.movies, 'movie');
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
            window.location.href = `allmovie.php?p=${currentPage - 1}`;
        }
    });

    document.getElementById('next-page').addEventListener('click', () => {
        if (currentPage < totalPages) {
            window.location.href = `allmovie.php?p=${currentPage + 1}`;
        }
    });

    document.addEventListener('DOMContentLoaded', fetchMovieList);

    // renderGrid is globally available from footer.php
</script>
