const SEARCH_API_URL = '/api/anime-world-india/v1/search.php?query=';
let searchTimeout;

// Global search toggle
function toggleSearch(show) {
    const modal = document.getElementById('search-modal');
    const input = document.getElementById('search-input');
    if (show) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        input.focus();
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

// Close modal on Esc key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') toggleSearch(false);
});

// Redirect to search page
function performFullSearch() {
    const query = document.getElementById('search-input').value.trim();
    if (query) {
        window.location.href = `/search.php?q=${encodeURIComponent(query)}`;
    }
}

// Handle Search Typing
function handleSearch(query) {
    const grid = document.getElementById('search-grid');
    const placeholder = document.getElementById('search-placeholder');
    
    clearTimeout(searchTimeout);
    
    if (!query.trim()) {
        grid.innerHTML = '';
        placeholder.classList.remove('hidden');
        return;
    }

    searchTimeout = setTimeout(async () => {
        placeholder.classList.add('hidden');
        grid.innerHTML = `<div class="col-span-full py-20 text-center text-[var(--light-text)] animate-pulse">Searching...</div>`;
        
        try {
            const response = await fetch(`${SEARCH_API_URL}${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.results && data.results.length > 0) {
                renderGrid('search-grid', data.results, null, true);
            } else {
                grid.innerHTML = `<div class="col-span-full py-20 text-center text-[var(--light-text)]">No results found for "${query}"</div>`;
            }
        } catch (err) {
            grid.innerHTML = `<div class="col-span-full py-20 text-center text-[var(--accent-red)]">Error fetching search results</div>`;
        }
    }, 500);
}

function renderGrid(containerId, items, fixedType = null, isSearch = false) {
    const container = document.getElementById(containerId);
    if (!container) return; // Only render if the container exists
    container.innerHTML = '';

    items.forEach(item => {
        const type = fixedType || item.type;
        const id = type === 'series' ? item.seriesId : item.movieId;
        const redirectUrl = type === 'series' ? `/series.php?${id}` : `/movie.php?${id}`;
        
        const card = document.createElement('a');
        card.href = redirectUrl;
        card.className = 'anime-card group relative cursor-pointer block';
        
        card.innerHTML = `
            <div class="relative aspect-[2/3] rounded-xl overflow-hidden shadow-lg bg-[var(--dark-slate)]">
                <img 
                    src="${item.image}" 
                    alt="${item.title}" 
                    class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500"
                    onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'"
                >
                <!-- Overlay -->
                <div class="play-overlay absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300 flex items-center justify-center">
                    <div class="w-12 h-12 bg-[var(--primary-red)] rounded-full flex items-center justify-center text-white scale-75 group-hover:scale-100 transition-transform duration-300">
                        <i class="fas fa-play ml-1"></i>
                    </div>
                </div>
                <!-- Badges -->
                <div class="absolute top-2 right-2 flex flex-col items-end gap-1">
                    <span class="bg-black/70 backdrop-blur-md text-[10px] font-bold px-2 py-1 rounded text-yellow-400 border border-yellow-400/30">
                        ${item.rating ? item.rating.replace('TMDB ', '') : '0.0'}
                    </span>
                    ${isSearch ? `<span class="bg-[var(--primary-red)]/90 backdrop-blur-md text-[9px] font-bold px-2 py-0.5 rounded text-white uppercase tracking-tighter">${type}</span>` : ''}
                </div>
                <div class="absolute bottom-2 left-2">
                    <span class="bg-[var(--primary-red)] text-[10px] font-bold px-2 py-0.5 rounded text-white uppercase tracking-wider">
                        ${item.year ? item.year.split('-')[0] : 'N/A'}
                    </span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-sm font-semibold line-clamp-2 group-hover:text-[var(--accent-red)] transition-colors">
                    ${item.title}
                </h3>
            </div>
        `;
        
        container.appendChild(card);
    });
}