<?php
require_once '../../config/App.php';

$resObj = new Reservation();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$sportifId = $input['sportif_id'] ?? null;
if (!$sportifId) {
    if (isset($_SESSION['user_id'])) {
        $sportifId = $_SESSION['user_id'];
    } elseif (isset($_SESSION['user']['id'])) {
        $sportifId = $_SESSION['user']['id'];
    }
}

$coachId = isset($input['coach_id']) ? (int)$input['coach_id'] : null;
$availabilityId = isset($input['availability_id']) ? (int)$input['availability_id'] : null;
$price = isset($input['price']) ? (float)$input['price'] : 0.00;

if (!$sportifId || !$coachId || !$availabilityId) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields (IDs)']);
    exit;
}

$newId = $resObj->create((int)$sportifId, (int)$coachId, (int)$availabilityId, (float)$price);

if ($newId) {
    echo json_encode([
        'success' => true,
        'id' => $newId,
        'message' => 'Reservation created successfully'
    ]);
} else {

    echo json_encode([
        'success' => false,
        'message' => 'Failed to create reservation. The slot might be already taken or a database error occurred.'
    ]);
}
