<?php include 'assets/html/header.php'; ?>

<main class="container mx-auto px-4 py-8 flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-[var(--dark-slate)] p-8 rounded-xl shadow-xl border border-[var(--border-color)] w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Register for ANIME<span class="text-[var(--accent-red)]">WORLD</span></h2>

        <form class="space-y-6">
            <div>
                <label for="username" class="block text-[var(--light-text)] text-sm font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" placeholder="Your Username"
                       class="w-full bg-[var(--dark-bg)] border border-[var(--border-color)] rounded-lg py-3 px-4 focus:outline-none focus:border-[var(--primary-red)] transition"
                       required>
            </div>
            <div>
                <label for="email" class="block text-[var(--light-text)] text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" placeholder="your@example.com"
                       class="w-full bg-[var(--dark-bg)] border border-[var(--border-color)] rounded-lg py-3 px-4 focus:outline-none focus:border-[var(--primary-red)] transition"
                       required>
            </div>
            <div>
                <label for="password" class="block text-[var(--light-text)] text-sm font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="********"
                       class="w-full bg-[var(--dark-bg)] border border-[var(--border-color)] rounded-lg py-3 px-4 focus:outline-none focus:border-[var(--primary-red)] transition"
                       required>
            </div>
            <div>
                <label for="confirm-password" class="block text-[var(--light-text)] text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="********"
                       class="w-full bg-[var(--dark-bg)] border border-[var(--border-color)] rounded-lg py-3 px-4 focus:outline-none focus:border-[var(--primary-red)] transition"
                       required>
            </div>
            <button type="submit" class="w-full bg-[var(--primary-red)] hover:bg-[var(--accent-red)] text-white font-bold py-3 rounded-lg transition">
                Register
            </button>
        </form>

        <p class="text-center text-[var(--light-text)] text-sm mt-6">
            Already have an account? <a href="login.php" class="text-[var(--accent-red)] hover:underline">Login here</a>
        </p>
    </div>
</main>

<?php include 'assets/html/footer.php'; ?>
