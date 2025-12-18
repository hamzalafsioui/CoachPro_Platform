<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

$user = $_SESSION['user'];

// Mock Coaches Data
$coaches = [
    [
        'id' => 1,
        'name' => 'Coach Alex Doe',
        'rating' => 4.8,
        'reviews' => 124,
        'specialties' => ['Personal Training', 'Crossfit'],
        'bio' => 'Certified personal trainer with 5 years of experience helping people achieve their fitness goals.',
        'image' => 'fas fa-user-ninja'
    ],
    [
        'id' => 2,
        'name' => 'Sarah Miller',
        'rating' => 4.9,
        'reviews' => 89,
        'specialties' => ['Yoga', 'Pilates', 'Meditation'],
        'bio' => 'Passionate yoga instructor focused on mindfulness and flexibility improving.',
        'image' => 'fas fa-leaf'
    ],
    [
        'id' => 3,
        'name' => 'Mike Tyson (Not that one)',
        'rating' => 4.7,
        'reviews' => 56,
        'specialties' => ['Boxing', 'HIIT'],
        'bio' => 'High intensity boxing coach that will push you to your absolute limits.',
        'image' => 'fas fa-fist-raised'
    ],
    [
        'id' => 4,
        'name' => 'Emma Watson',
        'rating' => 5.0,
        'reviews' => 210,
        'specialties' => ['Nutrition', 'Wellness', 'Personal Training'],
        'bio' => 'Holistic approach to health combining fitness with proper nutrition guidance.',
        'image' => 'fas fa-apple-alt'
    ],
    [
        'id' => 5,
        'name' => 'John Wick',
        'rating' => 4.6,
        'reviews' => 45,
        'specialties' => ['Self Defense', 'Judo'],
        'bio' => 'Focus.',
        'image' => 'fas fa-user-shield'
    ],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Coaches - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/sportif_coaches.css">
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

                <a href="dashboard.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
                    <i class="fas fa-th-large w-6 text-center group-hover:text-blue-400 transition-colors"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="coaches.php" class="sidebar-link active flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group text-gray-400">
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">Find a Coach</h1>
                    <p class="text-gray-400">Discover top-rated coaches tailored to your goals.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row gap-4 items-center">
                <div class="search-input-wrapper flex-1 w-full">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    <input type="text" id="coachSearch" placeholder="Search by name..." class="w-full bg-gray-800 border-none rounded-xl py-3 pl-12 pr-4 text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="w-full md:w-auto">
                    <select id="specialtyFilter" class="w-full bg-gray-800 text-white border-none rounded-xl py-3 px-6 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="all">All Specialties</option>
                        <option value="personal training">Personal Training</option>
                        <option value="yoga">Yoga</option>
                        <option value="pilates">Pilates</option>
                        <option value="hiit">HIIT</option>
                        <option value="boxing">Boxing</option>
                        <option value="nutrition">Nutrition</option>
                    </select>
                </div>
            </div>

            <!-- Coaches Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($coaches as $coach): ?>
                    <div class="glass-panel rounded-2xl p-6 coach-card group relative" data-name="<?php echo htmlspecialchars($coach['name']); ?>" data-specialties="<?php echo strtolower(implode(' ', $coach['specialties'])); ?>" data-id="<?php echo $coach['id']; ?>">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-16 h-16 rounded-xl bg-gray-800 flex items-center justify-center text-3xl text-gray-400 coach-img-wrapper">
                                <i class="<?php echo $coach['image']; ?> coach-img"></i>
                            </div>
                            <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded-lg border border-yellow-500/20">
                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <span class="font-bold text-yellow-500 text-sm"><?php echo $coach['rating']; ?></span>
                                <span class="text-gray-500 text-xs">(<?php echo $coach['reviews']; ?>)</span>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-1"><?php echo htmlspecialchars($coach['name']); ?></h3>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php foreach ($coach['specialties'] as $skill): ?>
                                <span class="text-xs px-2 py-1 rounded-md specialty-tag">
                                    <?php echo htmlspecialchars($skill); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-gray-400 text-sm mb-6 line-clamp-2">
                            <?php echo htmlspecialchars($coach['bio']); ?>
                        </p>

                        <button onclick="handleBooking(<?php echo $coach['id']; ?>)" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20 book-btn">
                            Book Session
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination (Mock) -->
            <div class="flex justify-center mt-8">
                <nav class="flex gap-2">
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-800 text-gray-500 hover:text-white disabled:opacity-50" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white font-bold">1</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">2</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">3</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </nav>
            </div>
        </div>
    </main>

    <!-- Specific JS -->
    <script src="../../assets/js/sportif_coaches.js"></script>

</body>

</html>