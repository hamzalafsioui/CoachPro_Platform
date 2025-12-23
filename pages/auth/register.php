<?php require_once '../../config/App.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CoachPro</title>
    <meta name="description" content="Create your CoachPro account and start your fitness journey today.">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/tailwind.config.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">


</head>

<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <!-- Registration Container -->
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="../../index.php" class="inline-flex items-center space-x-3 mb-6">
                <div class="w-14 h-14 gradient-blue rounded-lg flex items-center justify-center pulse-glow">
                    <i class="fas fa-dumbbell text-white text-2xl"></i>
                </div>
                <span class="text-white text-3xl font-outfit font-bold">CoachPro</span>
            </a>
            <h1 class="text-4xl font-outfit font-bold text-white mb-2">Create Account</h1>
            <p class="text-gray-300">Start your fitness journey today</p>
        </div>

        <!-- Registration Form -->
        <div class="glass-dark rounded-2xl p-8 shadow-2xl">
            <?php
            // Display error messages if any
            if (isset($_SESSION['error'])) {
                echo '<div class="mb-6 p-4 rounded-lg bg-red-500/20 border border-red-500/50 text-red-200">
                        <i class="fas fa-exclamation-circle mr-2"></i>' . htmlspecialchars($_SESSION['error']) . '
                      </div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="mb-6 p-4 rounded-lg bg-green-500/20 border border-green-500/50 text-green-200">
                        <i class="fas fa-check-circle mr-2"></i>' . htmlspecialchars($_SESSION['success']) . '
                      </div>';
                unset($_SESSION['success']);
            }
            ?>

            <form id="registerForm" action="../../actions/auth/register.action.php" method="POST" class="space-y-5">

                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i> First Name
                        </label>
                        <input type="text"
                            id="firstname"
                            name="firstname"
                            class="form-input"
                            placeholder="John"
                            required>
                        <span class="error-message hidden" id="firstname-error"></span>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i> Last Name
                        </label>
                        <input type="text"
                            id="lastname"
                            name="lastname"
                            class="form-input"
                            placeholder="Doe"
                            required>
                        <span class="error-message hidden" id="lastname-error"></span>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-1"></i> Email Address
                    </label>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="john.doe@example.com"
                        required>
                    <span class="error-message hidden" id="email-error"></span>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-phone mr-1"></i> Phone Number <span class="text-gray-500">(Optional)</span>
                    </label>
                    <input type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        placeholder="+1 234 567 8900">
                    <span class="error-message hidden" id="phone-error"></span>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-lock mr-1"></i> Password
                    </label>
                    <div class="relative">
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-input pr-10"
                            placeholder="Min. 8 characters"
                            required>
                        <button type="button"
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <span class="error-message hidden" id="password-error"></span>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2 h-1 bg-gray-700 rounded-full overflow-hidden">
                        <div id="password-strength" class="h-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-lock mr-1"></i> Confirm Password
                    </label>
                    <div class="relative">
                        <input type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="form-input pr-10"
                            placeholder="Re-enter password"
                            required>
                        <button type="button"
                            onclick="togglePassword('confirm_password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                            <i class="fas fa-eye" id="confirm_password-icon"></i>
                        </button>
                    </div>
                    <span class="error-message hidden" id="confirm-password-error"></span>
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">
                        <i class="fas fa-user-tag mr-1"></i> I want to register as:
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Sportif Option -->
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                name="role"
                                value="sportif"
                                class="peer sr-only"
                                checked>
                            <div class="glass border-2 border-transparent peer-checked:border-blue-500 rounded-xl p-4 text-center smooth-transition hover:border-blue-400">
                                <i class="fas fa-running text-3xl text-blue-400 mb-2"></i>
                                <div class="text-white font-semibold">Athlete</div>
                                <div class="text-xs text-gray-400">Find coaches</div>
                            </div>
                        </label>

                        <!-- Coach Option -->
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                name="role"
                                value="coach"
                                class="peer sr-only">
                            <div class="glass border-2 border-transparent peer-checked:border-cyan-500 rounded-xl p-4 text-center smooth-transition hover:border-cyan-400">
                                <i class="fas fa-user-tie text-3xl text-cyan-400 mb-2"></i>
                                <div class="text-white font-semibold">Coach</div>
                                <div class="text-xs text-gray-400">Offer sessions</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full gradient-blue py-3 rounded-lg text-white font-semibold text-lg hover-scale smooth-transition shadow-lg flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Create Account</span>
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t border-gray-700">
                    <p class="text-gray-400">
                        Already have an account?
                        <a href="login.php" class="text-blue-400 hover:text-blue-300 font-semibold smooth-transition">
                            Sign In
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="../../index.php" class="text-gray-400 hover:text-white smooth-transition inline-flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../../assets/js/main.js"></script>

</body>

</html>