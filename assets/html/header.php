<?php
// assets/html/header.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime World India</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="custom-scrollbar">

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-[var(--dark-slate)]/90 backdrop-blur-md border-b border-[var(--border-color)]">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2">
                <div class="bg-[var(--primary-red)] p-2 rounded-lg">
                    <i class="fas fa-play text-white"></i>
                </div>
                <h1 class="text-xl font-bold tracking-tight">ANIME<span class="text-[var(--accent-red)]">WORLD</span></h1>
            </a>
            
            <nav class="hidden md:flex space-x-8 text-sm font-medium text-[var(--light-text)]">
                <a href="index.php" class="hover:text-[var(--accent-red)] transition">Home</a>
                <a href="allseries.php" class="hover:text-[var(--accent-red)] transition">Series</a>
                <a href="allmovie.php" class="hover:text-[var(--accent-red)] transition">Movies</a>
                <a href="profile.php" class="hover:text-[var(--accent-red)] transition">Profile</a>
            </nav>

            <div class="flex items-center space-x-4">
                <button onclick="toggleSearch(true)" class="p-2 text-[var(--light-text)] hover:text-white transition"><i class="fas fa-search"></i></button>
                <a href="login.php" class="bg-[var(--primary-red)] hover:bg-[var(--accent-red)] px-4 py-1.5 rounded-full text-sm font-semibold transition">Login</a>
            </div>
        </div>
    </header>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 z-[60] hidden flex-col">
        <div class="container mx-auto px-4 py-6 flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-8">
                <div class="flex-1 max-w-2xl relative flex gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-[var(--light-text)]"></i>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Search anime series or movies..." 
                            class="w-full bg-[var(--dark-slate)] border-2 border-[var(--border-color)] rounded-xl py-4 pl-12 pr-4 focus:outline-none focus:border-[var(--primary-red)] transition text-lg"
                            oninput="handleSearch(this.value)"
                            onkeydown="if(event.key === 'Enter') performFullSearch()"
                        >
                    </div>
                    <button 
                        onclick="performFullSearch()"
                        class="bg-[var(--primary-red)] hover:bg-[var(--accent-red)] text-white px-6 rounded-xl font-bold transition flex items-center gap-2 shrink-0"
                    >
                        <span>Search</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <button onclick="toggleSearch(false)" class="ml-4 p-4 text-[var(--light-text)] hover:text-white text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Search Results -->
            <div id="search-results-container" class="flex-1 overflow-y-auto custom-scrollbar">
                <div id="search-placeholder" class="text-center py-20 text-[var(--light-text)]">
                    <i class="fas fa-film text-6xl mb-4 block opacity-20"></i>
                    <p>Start typing to search across the multiverse...</p>
                </div>
                <div id="search-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 pb-10">
                    <!-- Dynamic Search Results -->
                </div>
            </div>
        </div>
    </div>