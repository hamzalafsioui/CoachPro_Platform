<?php

require_once '../../config/App.php';
// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

$user = $_SESSION['user'];
$sportif_id = $user['id'];
$sportifObj = new Sportif((int)$sportif_id);


// Get real data from functions
$stats = $sportifObj->getStats();
$upcoming_session = $sportifObj->getNextSession();
$recent_activities = $sportifObj->getRecentActivity();
$weekly_activity = $sportifObj->getWeeklyActivity();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/sportif_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/sportif_profile.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>

</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

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

                <a href="dashboard.php" class="sidebar-link active flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                    <i class="fas fa-th-large w-6 text-center group-hover:text-blue-400 transition-colors"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="coaches.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                    <i class="fas fa-search w-6 text-center group-hover:text-cyan-400 transition-colors"></i>
                    <span class="font-medium">Find Coaches</span>
                </a>

                <a href="reservations.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                    <i class="fas fa-clipboard-list w-6 text-center group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-medium">My Bookings</span>
                </a>

                <div class="pt-6 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Settings</p>
                </div>

                <a href="profile.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                    <i class="fas fa-user-cog w-6 text-center group-hover:text-blue-400 transition-colors"></i>
                    <span class="font-medium">Profile</span>
                </a>

                <a href="../auth/logout.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-red-500/10 hover:text-red-400 group text-gray-400">
                    <i class="fas fa-sign-out-alt w-6 text-center transition-colors"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php include '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
            <!-- Welcome Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 id="userGreeting" class="text-3xl font-outfit font-bold text-white mb-2">Welcome back, <?php echo htmlspecialchars($sportifObj->getFirstname()); ?>!</h1>
                    <p class="text-gray-400">Here's your daily fitness summary.</p>
                </div>
                <div class="flex gap-3">
                    <a href="coaches.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium transition-colors shadow-lg shadow-blue-600/20 text-sm flex items-center gap-2">
                        <i class="fas fa-search"></i> Find Coach
                    </a>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-panel p-6 rounded-2xl dashboard-stat-card flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div>
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Workouts</p>
                        <h3 class="text-3xl font-bold text-white mt-1"><?php echo $stats['workouts']; ?></h3>
                        <p class="text-green-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-arrow-up"></i> Keep it up!
                        </p>
                    </div>
                    <div class="metric-circle">
                        <svg width="80" height="80">
                            <circle class="metric-track" cx="40" cy="40" r="36" />
                            <circle class="metric-fill" cx="40" cy="40" r="36" data-percent="65" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-blue-400">
                            <i class="fas fa-dumbbell text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-6 rounded-2xl dashboard-stat-card flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div>
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Calories</p>
                        <h3 class="text-3xl font-bold text-white mt-1"><?php echo $stats['calories']; ?></h3>
                        <p class="text-green-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-arrow-up"></i> +12% vs last week
                        </p>
                    </div>
                    <div class="metric-circle">
                        <svg width="80" height="80">
                            <circle class="metric-track" cx="40" cy="40" r="36" />
                            <circle class="metric-fill" style="stroke: #f97316;" cx="40" cy="40" r="36" data-percent="82" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-orange-400">
                            <i class="fas fa-fire text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-6 rounded-2xl dashboard-stat-card flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-purple-500/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div>
                        <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Active Minutes</p>
                        <h3 class="text-3xl font-bold text-white mt-1"><?php echo $stats['active_minutes']; ?></h3>
                        <p class="text-gray-500 text-xs mt-2">Target: 450 min</p>
                    </div>
                    <div class="metric-circle">
                        <svg width="80" height="80">
                            <circle class="metric-track" cx="40" cy="40" r="36" />
                            <circle class="metric-fill" style="stroke: #a855f7;" cx="40" cy="40" r="36" data-percent="45" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-purple-400">
                            <i class="fas fa-stopwatch text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Weekly Activity Chart -->
                    <div class="glass-panel p-8 rounded-2xl">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-bold text-white">Weekly Activity</h3>
                            <select class="bg-gray-800 text-gray-400 text-sm border-none rounded-lg px-3 py-1 focus:ring-2 focus:ring-blue-500 outline-none">
                                <option>This Week</option>
                                <option>Last Week</option>
                            </select>
                        </div>
                        <div class="h-48 flex items-end justify-between gap-4 px-2">
                            <?php foreach ($weekly_activity as $day): ?>
                                <div class="flex flex-col items-center gap-3 flex-1 group cursor-pointer">
                                    <div class="w-full bg-gray-800 rounded-t-lg relative h-full flex items-end overflow-hidden">
                                        <div class="activity-bar w-full bg-blue-600/80 group-hover:bg-blue-500 transition-colors rounded-t-lg relative" data-height="<?php echo $day['height']; ?>"></div>
                                    </div>
                                    <span class="text-gray-500 text-xs font-medium"><?php echo $day['day']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Upcoming Session -->
                    <div class="upcoming-card rounded-2xl p-1 relative overflow-hidden">
                        <div class="glass-panel p-6 rounded-xl relative z-10">
                            <?php if ($upcoming_session): ?>
                                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-xl font-bold text-white shadow-lg">
                                            <?php echo htmlspecialchars($upcoming_session['avatar']); ?>
                                        </div>
                                        <div class="text-center md:text-left">
                                            <p class="text-blue-400 text-xs font-bold uppercase tracking-wide mb-1">Up Next</p>
                                            <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($upcoming_session['type']); ?></h3>
                                            <p class="text-gray-400 text-sm">with <?php echo htmlspecialchars($upcoming_session['coach']); ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-8 bg-black/20 px-6 py-3 rounded-xl border border-white/5">
                                        <div class="text-center">
                                            <p class="text-gray-400 text-xs uppercase">Time</p>
                                            <p class="text-white font-bold"><?php echo htmlspecialchars($upcoming_session['time']); ?></p>
                                        </div>
                                        <div class="w-px h-8 bg-gray-700"></div>
                                        <div class="text-center">
                                            <p class="text-gray-400 text-xs uppercase">Date</p>
                                            <p class="text-white font-bold"><?php echo htmlspecialchars($upcoming_session['date']); ?></p>
                                        </div>
                                    </div>

                                    <button class="px-6 py-2 bg-white text-gray-900 rounded-lg font-bold text-sm hover:bg-gray-100 transition-colors shadow-lg shadow-white/10">
                                        Join Now
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center py-4 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-800 flex items-center justify-center mb-3">
                                        <i class="fas fa-calendar-times text-gray-500"></i>
                                    </div>
                                    <h3 class="text-white font-bold">No Upcoming Sessions</h3>
                                    <p class="text-gray-500 text-sm">Book a session with a coach to get started!</p>
                                    <a href="coaches.php" class="mt-4 text-blue-400 text-sm font-medium hover:underline">Find a Coach</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- Recent Activity -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-6">Recent Activity</h3>
                        <div class="space-y-6">
                            <?php if (empty($recent_activities)): ?>
                                <p class="text-gray-500 text-sm text-center py-4">No recent activity found.</p>
                            <?php else: ?>
                                <?php foreach ($recent_activities as $index => $activity): ?>
                                    <div class="timeline-item pb-6 <?php echo $index === count($recent_activities) - 1 ? 'border-l-0' : ''; ?>">
                                        <h4 class="text-white font-medium text-sm"><?php echo htmlspecialchars($activity['title']); ?></h4>
                                        <p class="text-gray-500 text-xs mt-1"><?php echo htmlspecialchars($activity['date']); ?> &bull; <?php echo htmlspecialchars($activity['coach']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="w-full py-2 text-blue-400 text-sm font-medium hover:text-blue-300 transition-colors mt-2">
                            View All History
                        </button>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-panel p-6 rounded-2xl">
                        <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="#" class="quick-action-btn p-4 rounded-xl border border-gray-700 bg-gray-800/50 flex flex-col items-center justify-center gap-2 group">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-300">Log Workout</span>
                            </a>
                            <a href="#" class="quick-action-btn p-4 rounded-xl border border-gray-700 bg-gray-800/50 flex flex-col items-center justify-center gap-2 group">
                                <div class="w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-300">Favorites</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Specific JS -->
    <script src="../../assets/js/sportif_dashboard.js"></script>
</body>

</html>