<?php
session_start();
require_once '../../functions/availability.functions.php';
require_once '../../functions/coach.functions.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coach') {
    header('Location: ../../index.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$coach_name = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname'];
$coachId = getCoachIdByUserId($userId);

if (!$coachId) {
    die("Coach profile not found.");
}

$availability = getCoachRecurringSchedule($coachId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability - CoachPro</title>

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/coach_availability.css">

    <!-- Global Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>

</head>

<body class="text-gray-300 font-inter antialiased min-h-screen flex">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden glass-panel" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?php require_once '../../includes/coach_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-y-auto h-screen scroll-smooth">
        <!-- Top Bar -->
        <?php require_once '../../includes/header.php'; ?>

        <div class="p-8 max-w-6xl mx-auto space-y-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">Availability Settings</h1>
                    <p class="text-gray-400">Set your weekly recurring schedule.</p>
                </div>
                <button onclick="saveAvailability()" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white px-8 py-3 rounded-xl font-medium shadow-lg shadow-blue-500/25 transition-all transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>

            <!-- Weekly Schedule -->
            <div class="space-y-6">
                <?php foreach ($availability as $day => $data): ?>
                    <div class="glass-panel p-6 rounded-2xl day-card" id="<?php echo $day; ?>-card">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <!-- Day Toggle -->
                            <div class="w-full md:w-48 flex items-center justify-between md:justify-start gap-4">
                                <h3 class="capitalize font-bold text-lg text-white w-24"><?php echo $day; ?></h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="<?php echo $day; ?>-toggle" class="sr-only peer" <?php echo $data['active'] ? 'checked' : ''; ?>>
                                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Time Slots -->
                            <div class="flex-1 <?php echo $data['active'] ? '' : 'opacity-50 pointer-events-none'; ?>" id="<?php echo $day; ?>-slots">
                                <div id="<?php echo $day; ?>-slots-container">
                                    <?php if (empty($data['slots'])): ?>
                                    <?php endif; ?>

                                    <?php foreach ($data['slots'] as $slot): ?>
                                        <div class="flex items-center gap-2 mb-2 time-slot">
                                            <input type="time" name="<?php echo $day; ?>_start[]" value="<?php echo $slot[0]; ?>" class="time-input rounded-lg px-3 py-2 text-sm w-32">
                                            <span class="text-gray-500">-</span>
                                            <input type="time" name="<?php echo $day; ?>_end[]" value="<?php echo $slot[1]; ?>" class="time-input rounded-lg px-3 py-2 text-sm w-32">
                                            <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 p-2 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <button type="button" id="<?php echo $day; ?>-add-btn" class="mt-2 text-sm text-blue-400 hover:text-blue-300 flex items-center gap-1 transition-colors <?php echo $data['active'] ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $data['active'] ? '' : 'disabled'; ?>>
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add Interval</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- JS -->
    <script src="../../assets/js/coach_availability.js"></script>
</body>

</html>