<?php
session_start();
require_once '../../functions/auth.functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/auth/login.php');
    exit();
}

// variables for inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation errors array
$errors = [];

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

// Validate password
if (empty($password)) {
    $errors[] = "Password is required.";
}

// If there are validation errors => Redirect back with errors
if (!empty($errors)) {
    $_SESSION['error'] = implode(' & ', $errors);
    header('Location: ../../pages/auth/login.php');
    exit();
}

// Use the loginUser function from auth.functions.php
$result = loginUser($email, $password);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
    header('Location: ../../index.php');
} else {
    $_SESSION['error'] = $result['message'];
    header('Location: ../../pages/auth/login.php');
}
exit();
