<?php
session_start();
require_once '../../config/App.php';
$userObj = new User();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    //  validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: ../../pages/coach/profile.php?status=error&message=All fields are required");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header("Location: ../../pages/coach/profile.php?status=error&message=New passwords do not match");
        exit();
    }

    if (!$userObj->verifyPassword($userId, $currentPassword)) {
        header("Location: ../../pages/coach/profile.php?status=error&message=Incorrect current password");
        exit();
    }

    if ($userObj->updatePassword($userId, $newPassword)) {
        header("Location: ../../pages/coach/profile.php?status=success&message=Password updated successfully");
    } else {
        header("Location: ../../pages/coach/profile.php?status=error&message=Failed to update password");
    }
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
