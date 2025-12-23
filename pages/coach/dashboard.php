<?php
require_once '../../config/App.php';

// Authentication
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$coachObj = new Coach((int)$userId);
$statsObj = new Stats();
$resObj = new Reservation();

$coach_id = $coachObj->getCoachId();

if ($coach_id) {
    $stats = $statsObj->getCoachStats($coach_id);
    $upcoming_sessions = $resObj->getCoachUpcomingSessions($coach_id);
} else {
    $stats = ['total_sessions' => 0, 'total_clients' => 0, 'rating' => 0];
    $upcoming_sessions = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - CoachPro</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/coach_dashboard.css">

    <!-- Global Tailwind Config -->
    <script src="../../assets/js/tailwind.config.js"></script>

</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php include '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php include '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-8">
            <!-- Welcome Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">Welcome back, <?php echo htmlspecialchars($coachObj->getFirstname()); ?>! 👋</h1>
                    <p class="text-gray-400">Here's what's happening with your schedule today.</p>
                </div>
                <a href="schedule.php?action=new" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white px-6 py-3 rounded-lg font-medium shadow-lg shadow-blue-500/25 transition-all transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>New Session</span>
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mb-4 text-blue-400">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm font-medium mb-1">Total Sessions</p>
                        <h3 class="text-3xl font-bold text-white font-outfit"><?php echo $stats['total_sessions']; ?></h3>
                        <p class="text-blue-400 text-xs mt-2 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                        </p>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-purple-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mb-4 text-purple-400">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm font-medium mb-1">Active Clients</p>
                        <h3 class="text-3xl font-bold text-white font-outfit"><?php echo $stats['total_clients']; ?></h3>
                        <p class="text-purple-400 text-xs mt-2 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 5 new this week
                        </p>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-yellow-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-4 text-yellow-400">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm font-medium mb-1">Average Rating</p>
                        <h3 class="text-3xl font-bold text-white font-outfit"><?php echo $stats['rating']; ?></h3>
                        <p class="text-yellow-400 text-xs mt-2 flex items-center">
                            <i class="fas fa-star mr-1"></i> Based on 84 reviews
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity / Upcoming Sessions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Upcoming Sessions List -->
                <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white font-outfit">Upcoming Sessions</h3>
                        <a href="schedule.php" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">View All</a>
                    </div>

                    <div class="space-y-4">
                        <?php foreach ($upcoming_sessions as $session): ?>
                            <div class="bg-gray-800/40 hover:bg-gray-800/60 p-4 rounded-xl transition-colors border border-gray-700/50 flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-lg font-bold text-gray-300">
                                        <?php echo substr($session['client'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-white group-hover:text-blue-400 transition-colors"><?php echo $session['client']; ?></h4>
                                        <p class="text-sm text-gray-500"><?php echo $session['type']; ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-white font-medium"><?php echo $session['time']; ?></p>
                                    <p class="text-xs text-gray-400"><?php echo $session['date']; ?></p>
                                </div>
                                <!-- Status Badge -->
                                <div class="hidden sm:block">
                                    <?php if ($session['status'] === 'Confirmed'): ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                            Confirmed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Actions / Mini Schedule -->
                <div class="glass-panel rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-white font-outfit mb-6">Quick Actions</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-blue-600/20 to-blue-400/10 border border-blue-500/20 hover:border-blue-500/50 transition-all group">
                            <div class="flex justify-between items-center mb-2">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-600 group-hover:text-blue-400 transition-colors"></i>
                            </div>
                            <p class="font-semibold text-white">Create Schedule</p>
                            <p class="text-xs text-gray-500 mt-1">Add new slots for booking</p>
                        </button>

                        <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-purple-600/20 to-purple-400/10 border border-purple-500/20 hover:border-purple-500/50 transition-all group">
                            <div class="flex justify-between items-center mb-2">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-600 group-hover:text-purple-400 transition-colors"></i>
                            </div>
                            <p class="font-semibold text-white">Update Profile</p>
                            <p class="text-xs text-gray-500 mt-1">Keep your bio current</p>
                        </button>

                        <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-cyan-600/20 to-cyan-400/10 border border-cyan-500/20 hover:border-cyan-500/50 transition-all group">
                            <div class="flex justify-between items-center mb-2">
                                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                                    <i class="fas fa-star"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-600 group-hover:text-cyan-400 transition-colors"></i>
                            </div>
                            <p class="font-semibold text-white">Read Reviews</p>
                            <p class="text-xs text-gray-500 mt-1">See client feedback</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>

</html>