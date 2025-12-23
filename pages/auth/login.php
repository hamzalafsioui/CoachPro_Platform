<?php require_once '../../config/App.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CoachPro</title>
    <meta name="description" content="Sign in to your CoachPro account and continue your fitness journey.">

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

    <script src="../../assets/js/tailwind.config.js"></script>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <!-- Login Container -->
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="../../index.php" class="inline-flex items-center space-x-3 mb-6">
                <div class="w-14 h-14 gradient-blue rounded-lg flex items-center justify-center pulse-glow">
                    <i class="fas fa-dumbbell text-white text-2xl"></i>
                </div>
                <span class="text-white text-3xl font-outfit font-bold">CoachPro</span>
            </a>
            <h1 class="text-4xl font-outfit font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-gray-300">Sign in to continue your fitness journey</p>
        </div>

        <!-- Login Form -->
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

            <form id="loginForm" action="../../actions/auth/login.action.php" method="POST" class="space-y-5">

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
                        required
                        autocomplete="email">
                    <span class="error-message hidden" id="email-error"></span>
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
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password">
                        <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <span class="error-message hidden" id="password-error"></span>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-800 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-300">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300 smooth-transition">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full gradient-blue py-3 rounded-lg text-white font-semibold text-lg hover-scale smooth-transition shadow-lg flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>

                <!-- Register Link -->
                <div class="text-center pt-4 border-t border-gray-700">
                    <p class="text-gray-400">
                        Don't have an account?
                        <a href="register.php" class="text-blue-400 hover:text-blue-300 font-semibold smooth-transition">
                            Create Account
                        </a>
                    </p>
                </div>
            </form>

            <!-- Social Login Options (Optional) -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-gray-800/50 text-gray-400">Or continue with</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <button type="button" class="glass py-2.5 px-4 rounded-lg hover:bg-white/10 smooth-transition flex items-center justify-center space-x-2">
                        <i class="fab fa-google text-red-400"></i>
                        <span class="text-white text-sm font-medium">Google</span>
                    </button>
                    <button type="button" class="glass py-2.5 px-4 rounded-lg hover:bg-white/10 smooth-transition flex items-center justify-center space-x-2">
                        <i class="fab fa-facebook text-blue-400"></i>
                        <span class="text-white text-sm font-medium">Facebook</span>
                    </button>
                </div>
            </div>
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
    <script src="../../assets/js/login.js"></script>
</body>

</html>