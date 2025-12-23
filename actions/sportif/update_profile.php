<?php
require_once __DIR__ . '/../../config/App.php';

$userId = $_SESSION['user']['id'];
$sportifObj = new Sportif((int)$userId);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($firstname) || empty($lastname)) {
    echo json_encode(['success' => false, 'message' => 'First name and Last name are required']);
    exit;
}

$data = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'phone' => $phone
];
if ($sportifObj->update($data)) {
    // Update session
    $_SESSION['user']['firstname'] = $firstname;
    $_SESSION['user']['lastname'] = $lastname;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
