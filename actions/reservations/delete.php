<?php
session_start();
require_once '../../functions/reservation.functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$reservationId = $input['id'] ?? null;

if (!$reservationId) {
    echo json_encode(['success' => false, 'message' => 'Missing reservation ID']);
    exit;
}

if (deleteReservation($reservationId)) {
    echo json_encode(['success' => true, 'message' => 'Reservation deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete reservation']);
}
