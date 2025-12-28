<?php
require_once '../../config/App.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

$user = $_SESSION['user'];
$sportif_id = $user['id'];

$coach_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$coachObj = new Coach();
$coach = $coachObj->getProfileWithSports($coach_id);

if (!$coach) {
    header('Location: coaches.php');
    exit();
}

$availabilityObj = new Availability();
$raw_availabilities = $availabilityObj->getByCoach($coach_id);
$availability_by_date = [];

foreach ($raw_availabilities as $row) {
    if (!$row['is_available']) continue;

    // Only show slots from today onwards
    if (strtotime((string)$row['date']) < strtotime(date('Y-m-d'))) continue;

    $date_key = (string)$row['date'];
    if (!isset($availability_by_date[$date_key])) {
        $availability_by_date[$date_key] = [];
    }

    $availability_by_date[$date_key][] = [
        'id' => (int)$row['id'],
        'time' => date('H:i', strtotime((string)$row['start_time'])),
        'date' => $row['date']
    ];
}

// Sort by date key
ksort($availability_by_date);
// Limit to first 14 days with slots
$availability_by_date = array_slice($availability_by_date, 0, 14, true);

// Get real reviews from database
$reviewObj = new Review();
$reviews = $reviewObj->getCoachReviews($coach_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $coach['name']; ?> - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/sportif_coach_details.css">
    <link rel="stylesheet" href="../../assets/css/sportif_profile.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>

</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar (Shared navigation) -->
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
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth relative">
        <!-- Top Bar -->
        <header class="h-20 glass-panel border-b border-gray-800 sticky top-0 z-30 px-8 flex items-center justify-between">
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <a href="coaches.php" class="text-gray-400 hover:text-white flex items-center gap-2 text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left"></i> Back to Coaches
            </a>
            <div class="flex items-center space-x-6 ml-auto">
                <div class="flex items-center gap-3 pl-6 border-l border-gray-700">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-xs">
                        <?php echo substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1); ?>
                    </div>
                    <span class="text-sm font-medium text-white hidden md:block"><?php echo htmlspecialchars($user['firstname']); ?></span>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <div class="hero-banner relative p-8 md:p-12 mb-8">
            <div class="absolute inset-0 bg-blue-600/10 backdrop-blur-sm z-0"></div>
            <div class="relative z-10 max-w-5xl mx-auto flex flex-col md:flex-row items-center md:items-end gap-8">
                <div class="w-40 h-40 rounded-2xl bg-gray-800 border-4 border-gray-900 flex items-center justify-center text-5xl text-gray-500 shadow-2xl profile-avatar-lg">
                    <i class="<?php echo $coach['image']; ?>"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                        <h1 class="text-4xl font-outfit font-bold text-white"><?php echo $coach['name']; ?></h1>
                        <i class="fas fa-check-circle text-blue-500 text-xl" title="Verified Coach"></i>
                    </div>

                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mb-4">
                        <?php foreach ($coach['specialties'] as $skill): ?>
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-sm font-medium border border-blue-500/20">
                                <?php echo htmlspecialchars($skill); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex justify-center md:justify-start gap-6 text-sm">
                        <div class="stat-item pr-6">
                            <span class="block text-xl font-bold text-yellow-500 flex items-center gap-1">
                                <?php echo $coach['rating']; ?> <i class="fas fa-star text-sm"></i>
                            </span>
                            <span class="text-gray-400">Rating</span>
                        </div>
                        <div class="stat-item pr-6">
                            <span class="block text-xl font-bold text-white"><?php echo $coach['reviews_count']; ?></span>
                            <span class="text-gray-400">Reviews</span>
                        </div>
                        <div class="stat-item">
                            <span class="block text-xl font-bold text-green-400"><?php echo $coach['hourly_rate']; ?></span>
                            <span class="text-gray-400">per hour</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-8 pb-32 flex flex-col lg:flex-row gap-8">
            <!-- Left Column: Content -->
            <div class="flex-1">
                <!-- Tabs -->
                <div class="flex border-b border-gray-800 mb-8 overflow-x-auto">
                    <button type="button" class="tab-btn active px-6 py-4 text-font-medium font-outfit text-white whitespace-nowrap" data-target="about-content">About</button>
                    <button type="button" class="tab-btn px-6 py-4 text-font-medium font-outfit text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-target="schedule-content">Schedule</button>
                    <button type="button" class="tab-btn px-6 py-4 text-font-medium font-outfit text-gray-400 hover:text-white transition-colors whitespace-nowrap" data-target="reviews-content">Reviews</button>
                </div>

                <!-- Tab Contents -->
                <div id="about-content" class="content-section space-y-8 animate-fade-in">
                    <section>
                        <h3 class="text-xl font-bold text-white mb-4">Biography</h3>
                        <p class="text-gray-400 leading-relaxed"><?php echo $coach['bio']; ?></p>
                    </section>

                    <section>
                        <h3 class="text-xl font-bold text-white mb-4">Certifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($coach['certifications'] as $cert): ?>
                                <div class="flex items-center gap-3 p-4 glass-panel rounded-xl">
                                    <i class="fas fa-certificate text-yellow-500"></i>
                                    <span class="text-gray-300"><?php echo htmlspecialchars($cert); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>

                <div id="schedule-content" class="content-section hidden space-y-8 animate-fade-in">
                    <div class="glass-panel p-6 rounded-2xl border border-blue-500/20 bg-blue-500/5">
                        <div class="flex items-start gap-4">
                            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                            <div>
                                <h4 class="text-white font-bold mb-1">Select a time slot</h4>
                                <p class="text-sm text-gray-400">Choose a time that works for you. This will be your requested session time.</p>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($availability_by_date)): ?>
                        <div class="text-center py-12 glass-panel rounded-2xl">
                            <i class="fas fa-calendar-times text-4xl text-gray-600 mb-4 block"></i>
                            <p class="text-gray-400">No available slots found for the next two weeks.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($availability_by_date as $date => $slots): ?>
                            <div class="mb-8">
                                <h4 class="text-white font-bold mb-4 flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-blue-400"></i>
                                    <?php echo date('l, M j', strtotime($date)); ?>
                                </h4>
                                <div class="schedule-grid">
                                    <?php foreach ($slots as $slot): ?>
                                        <button type="button" class="time-slot px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-300 font-medium hover:text-white transition-all hover:border-blue-500/50 active:scale-95"
                                            data-time="<?php echo $slot['time']; ?>"
                                            data-date="<?php echo $slot['date']; ?>"
                                            data-id="<?php echo $slot['id']; ?>">
                                            <?php echo $slot['time']; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="reviews-content" class="content-section hidden space-y-6 animate-fade-in">
                    <?php if (empty($reviews)): ?>
                        <div class="glass-panel p-8 rounded-2xl text-center">
                            <i class="fas fa-star text-4xl text-gray-600 mb-4 block"></i>
                            <p class="text-gray-400">No reviews yet. Be the first to review this coach!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="glass-panel p-6 rounded-2xl review-card">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-white"><?php echo htmlspecialchars($review['client']); ?></h4>
                                    <span class="text-xs text-gray-500"><?php echo htmlspecialchars($review['date']); ?></span>
                                </div>
                                <div class="flex items-center gap-1 text-yellow-500 text-sm mb-3">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < $review['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-gray-600"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($review['comment']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Booking Widget (Desktop Sticky) -->
            <div class="hidden lg:block w-80">
                <div class="glass-panel p-6 rounded-2xl sticky top-28 booking-panel">
                    <h3 class="text-xl font-bold text-white mb-4">Book Session</h3>
                    <div class="flex justify-between items-center mb-6 pb-6 border-b border-gray-700">
                        <span class="text-gray-400">Price</span>
                        <span class="text-2xl font-bold text-white"><?php echo $coach['hourly_rate']; ?></span>
                    </div>

                    <button id="bookSessionBtn" type="button" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20 mb-4 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Select a Time
                    </button>

                    <p class="text-xs text-center text-gray-500">
                        <i class="fas fa-lock mr-1"></i> Secure booking
                    </p>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Booking Bar -->
        <div class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-800 p-4 lg:hidden z-50 flex items-center justify-between glass-panel">
            <div>
                <span class="text-gray-400 text-xs uppercase">Total</span>
                <span class="block text-xl font-bold text-white"><?php echo $coach['hourly_rate']; ?></span>
            </div>
            <button onclick="document.querySelector('[data-target=\'schedule-content\']').click()" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-600/20">
                Choose Time
            </button>
        </div>

    </main>

    <!-- Specific JS -->
    <script src="../../assets/js/sportif_coach_details.js"></script>

</body>

</html>