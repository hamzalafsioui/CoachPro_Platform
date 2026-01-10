<?php
require_once '../../config/App.php';
$page_title = "My Schedule";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/coach_schedule.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>
</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php require '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php include_once '../../includes/header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">My Schedule</h1>
                    <p class="text-gray-400">View and manage your upcoming sessions.</p>
                </div>

                <div class="flex items-center bg-gray-800/50 rounded-lg p-1 border border-gray-700">
                    <button class="px-4 py-2 bg-gray-700 rounded-md text-white text-sm font-medium shadow">Month</button>
                    <button class="px-4 py-2 text-gray-400 hover:text-white text-sm font-medium transition-colors">Week</button>
                    <button class="px-4 py-2 text-gray-400 hover:text-white text-sm font-medium transition-colors">Day</button>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="flex flex-col">
                <!-- Calendar Header -->
                <div class="calendar-header p-6 flex items-center justify-between">
                    <div>
                        <h2 id="monthYear" class="text-2xl font-bold text-white font-outfit">December 2023</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="prevMonth()" class="p-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="nextMonth()" class="p-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Days Header -->
                <div class="grid grid-cols-7 border-l border-r border-gray-700/30">
                    <div class="weekday-header">Sun</div>
                    <div class="weekday-header">Mon</div>
                    <div class="weekday-header">Tue</div>
                    <div class="weekday-header">Wed</div>
                    <div class="weekday-header">Thu</div>
                    <div class="weekday-header">Fri</div>
                    <div class="weekday-header">Sat</div>
                </div>

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    <!-- Javascript will populate this -->
                </div>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-gray-400">Confirmed</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                    <span class="text-gray-400">Pending</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <span class="text-gray-400">Cancelled</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Specific JS -->
    <script src="../../assets/js/coach_schedule.js?v=<?php echo time(); ?>"></script>
</body>

</html>