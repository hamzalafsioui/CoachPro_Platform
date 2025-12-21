<?php
session_start();
require_once '../../functions/availability.functions.php';
require_once '../../functions/coach.functions.php';

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
$coachId = getCoachIdByUserId($userId);


if (!$coachId) {
    echo json_encode(['success' => false, 'message' => 'Coach profile not found']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$schedule = $input['schedule'] ?? null;

if (!$schedule) {
    echo json_encode(['success' => false, 'message' => 'Missing schedule data']);
    exit;
}

if (saveCoachAvailability($coachId, $schedule)) {
    echo json_encode(['success' => true, 'message' => 'Availability saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save availability']);
}
