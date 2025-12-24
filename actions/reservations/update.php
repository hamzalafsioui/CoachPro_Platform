<?php
require_once '../../config/App.php';

$resObj = new Reservation();

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
$action = $input['action'] ?? null;

if (!$reservationId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$status = '';
switch ($action) {
    case 'accept':
        $status = 'confirmed';
        break;
    case 'cancel':
    case 'decline':
        $status = 'cancelled';
        break;
    case 'complete':
        $status = 'completed';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

if ($resObj->updateStatus($status, (int)$reservationId)) {
    echo json_encode(['success' => true, 'message' => 'Reservation updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update reservation']);
}
