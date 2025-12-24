<?php
session_start();
require_once '../../config/App.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userObj = new User($userId);

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // redirect path based on role
    $redirectPath = '../../pages/coach/profile.php'; // default
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'sportif') {
        $redirectPath = '../../pages/sportif/profile.php';
    }

    //  validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: " . $redirectPath . "?status=error&message=All fields are required");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header("Location: " . $redirectPath . "?status=error&message=New passwords do not match");
        exit();
    }

    if (!$userObj->verifyPassword($currentPassword)) {
        header("Location: " . $redirectPath . "?status=error&message=Incorrect current password");
        exit();
    }

    if ($userObj->updatePassword($newPassword)) {
        header("Location: " . $redirectPath . "?status=success&message=Password updated successfully");
    } else {
        header("Location: " . $redirectPath . "?status=error&message=Failed to update password");
    }
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
