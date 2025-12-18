<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside id="sidebar" class="fixed lg:sticky top-0 h-screen w-72 glass-panel border-r border-gray-800 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-y-auto">
    <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center space-x-3 mb-10">
            <div class="w-10 h-10 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-dumbbell text-white text-xl"></i>
            </div>
            <span class="text-white text-xl font-outfit font-bold tracking-tight">CoachPro</span>
        </div>

        <!-- Navigation -->
        <nav class="space-y-2">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Menu</p>

            <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-th-large w-6 text-center <?php echo $current_page === 'dashboard.php' ? '' : 'group-hover:text-blue-400'; ?> transition-colors"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="schedule.php" class="sidebar-link <?php echo $current_page === 'schedule.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-calendar-alt w-6 text-center <?php echo $current_page === 'schedule.php' ? '' : 'group-hover:text-cyan-400'; ?> transition-colors"></i>
                <span class="font-medium">My Schedule</span>
            </a>

            <a href="availability.php" class="sidebar-link <?php echo $current_page === 'availability.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-clock w-6 text-center <?php echo $current_page === 'availability.php' ? '' : 'group-hover:text-green-400'; ?> transition-colors"></i>
                <span class="font-medium">Availability</span>
            </a>

            <a href="reservations.php" class="sidebar-link <?php echo $current_page === 'reservations.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-clipboard-list w-6 text-center <?php echo $current_page === 'reservations.php' ? '' : 'group-hover:text-indigo-400'; ?> transition-colors"></i>
                <span class="font-medium">Reservations</span>
            </a>

            <a href="clients.php" class="sidebar-link <?php echo $current_page === 'clients.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-users w-6 text-center <?php echo $current_page === 'clients.php' ? '' : 'group-hover:text-purple-400'; ?> transition-colors"></i>
                <span class="font-medium">Clients</span>
            </a>

            <a href="reviews.php" class="sidebar-link <?php echo $current_page === 'reviews.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-star w-6 text-center <?php echo $current_page === 'reviews.php' ? '' : 'group-hover:text-yellow-400'; ?> transition-colors"></i>
                <span class="font-medium">Reviews</span>
            </a>

            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Settings</p>
            </div>

            <a href="profile.php" class="sidebar-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?> flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                <i class="fas fa-user-cog w-6 text-center <?php echo $current_page === 'profile.php' ? '' : 'group-hover:text-blue-400'; ?> transition-colors"></i>
                <span class="font-medium">Profile</span>
            </a>

            <a href="../auth/logout.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-red-500/10 hover:text-red-400 group text-gray-400">
                <i class="fas fa-sign-out-alt w-6 text-center transition-colors"></i>
                <span class="font-medium">Logout</span>
            </a>
        </nav>
    </div>

    <!-- User Mini Profile -->
    <div class="absolute bottom-0 w-full p-6 border-t border-gray-800 bg-black/20">
        <div class="flex items-center space-x-3">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($coach_name ?? 'Coach'); ?>&background=0D8ABC&color=fff" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-700">
            <div>
                <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($coach_name ?? 'Coach Name'); ?></p>
                <p class="text-xs text-gray-500">Professional Coach</p>
            </div>
        </div>
    </div>
</aside>