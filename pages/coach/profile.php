<?php
session_start();
require_once '../../functions/coach.functions.php';

// check is user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$coach_profile = getCoachProfile($_SESSION["user_id"]);

// check is data Exists
if (!$coach_profile) {
    // Handle error or redirect
    die("Coach not found.");
}

$coach = [
    'name' => ($coach_profile['firstname'] ?? '') . ' ' . ($coach_profile['lastname'] ?? ''),
    'email' => $coach_profile['email'] ?? '',
    'bio' => $coach_profile['bio'] ?? 'No bio yet.',
    'specialties' => 'HIIT, Strength Training, Cardio', // Not Exist in DB => hardcoded 
    'phone' => $coach_profile['phone'] ?? '+212xxxxxxxx',
    'experience' => $coach_profile['experience_years'] ?? 0
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Profile - CoachPro</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/coach_profile.css">
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

        <div class="p-8 max-w-5xl mx-auto space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-outfit font-bold text-white mb-2">My Profile</h1>
                    <p class="text-gray-400">Manage your account information and preferences.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Profile Picture & Basic Actions -->
                <div class="space-y-6">
                    <div class="glass-panel p-6 rounded-2xl text-center">
                        <div class="relative w-32 h-32 mx-auto mb-4 group">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($coach['name']); ?>&background=0D8ABC&color=fff&size=200" alt="Profile" class="w-full h-full rounded-full border-4 border-gray-700 shadow-xl object-cover">
                            <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                <i class="fas fa-camera text-white text-2xl"></i>
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-white font-outfit"><?php echo htmlspecialchars($coach['name']); ?></h2>
                        <p class="text-blue-400 text-sm mb-4">Professional Coach</p>

                        <div class="flex justify-center gap-2">
                            <span class="px-3 py-1 bg-gray-700/50 rounded-full text-xs text-gray-300 border border-gray-600">HIIT</span>
                            <span class="px-3 py-1 bg-gray-700/50 rounded-full text-xs text-gray-300 border border-gray-600">Cardio</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Edit Form -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Personal Information -->
                    <div class="glass-panel p-8 rounded-2xl">
                        <h3 class="text-xl font-bold text-white font-outfit mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-3 text-blue-500"></i>
                            Personal Information
                        </h3>

                        <form action="../../actions/profile/update_coach_profile.php" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">Full Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($coach['name']); ?>" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">Email Address</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($coach['email']); ?>" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white" readonly>
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">Phone Number</label>
                                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($coach['phone']); ?>" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">Experience (Years)</label>
                                    <input type="number" name="experience" value="<?php echo htmlspecialchars($coach['experience']); ?>" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white">
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-400 text-sm font-medium mb-2">Specialties</label>
                                <input type="text" name="specialties" value="<?php echo htmlspecialchars($coach['specialties']); ?>" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white" readonly>
                                <p class="text-xs text-gray-500 mt-2">Specialties are currently managed by the administration.</p>
                            </div>

                            <div>
                                <label class="block text-gray-400 text-sm font-medium mb-2">Bio</label>
                                <textarea name="bio" rows="4" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white"><?php echo htmlspecialchars($coach['bio']); ?></textarea>

                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white px-8 py-3 rounded-xl font-medium shadow-lg shadow-blue-500/25 transition-all transform hover:scale-105">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security -->
                    <div class="glass-panel p-8 rounded-2xl">
                        <h3 class="text-xl font-bold text-white font-outfit mb-6 flex items-center">
                            <i class="fas fa-shield-alt mr-3 text-purple-500"></i>
                            Security
                        </h3>

                        <form action="../../actions/profile/update_password.php" method="POST" class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-400 text-sm font-medium mb-2">Current Password</label>
                                    <input type="password" name="current_password" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white" required>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-400 text-sm font-medium mb-2">New Password</label>
                                        <input type="password" name="new_password" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-sm font-medium mb-2">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="w-full form-input rounded-xl px-4 py-3 placeholder-gray-500 focus:text-white" required>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-medium transition-all">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/coach_profile.js"></script>
</body>

</html>