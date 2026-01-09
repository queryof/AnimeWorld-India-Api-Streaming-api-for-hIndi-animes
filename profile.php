<?php include 'assets/html/header.php'; ?>

<main class="container mx-auto px-4 py-8">
    <div class="bg-[var(--dark-slate)] p-8 rounded-xl shadow-xl border border-[var(--border-color)]">
        <h2 class="text-3xl font-bold mb-6">Welcome, <span class="text-[var(--accent-red)]">User!</span></h2>
        
        <div class="space-y-4 text-[var(--light-text)]">
            <p><strong>Email:</strong> user@example.com</p>
            <p><strong>Membership:</strong> Premium</p>
            <p>This is your profile page. You can customize your settings and view your watch history here.</p>
        </div>

        <div class="mt-8">
            <a href="#" class="bg-[var(--primary-red)] hover:bg-[var(--accent-red)] text-white px-6 py-2 rounded-lg text-sm font-bold transition">Edit Profile</a>
            <button class="ml-4 bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg text-sm font-bold transition">Logout</button>
        </div>
    </div>
</main>

<?php include 'assets/html/footer.php'; ?>
