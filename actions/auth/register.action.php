<?php
require_once '../../config/App.php';

$auth = new Auth();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/auth/register.php');
    exit();
}

// variables for inputs
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'sportif';

// errors array
$errors = [];

// validate first name
if (empty($firstname) || strlen($firstname) < 2) {
    $errors[] = "First name must be at least 2 characters long.";
}

// validate last name
if (empty($lastname) || strlen($lastname) < 2) {
    $errors[] = "Last name must be at least 2 characters long.";
}

// validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

// Validate password
if (empty($password) || strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

// Check if passwords match
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

// Validate role
if (!in_array($role, ['sportif', 'coach'])) {
    $errors[] = "Invalid role selected.";
}

// If there are validation errors=> Redirect back with errors
if (!empty($errors)) {
    $_SESSION['error'] = implode(' & ', $errors);
    header('Location: ../../pages/auth/register.php');
    exit();
}

// Use the registerUser function from auth.functions.php
$result = $auth->register($firstname, $lastname, $email, $phone, $password, $role);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
    session_write_close();
    header('Location: ../../pages/auth/login.php');
} else {
    $_SESSION['error'] = $result['message'];
    session_write_close();
    header('Location: ../../pages/auth/register.php');
}
exit();
