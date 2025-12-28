<?php
require_once '../../config/App.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in and is a coach
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coach') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$reviewId = isset($input['review_id']) ? (int)$input['review_id'] : null;
$replyText = isset($input['reply_text']) ? trim($input['reply_text']) : '';

// Validate input
if (!$reviewId || empty($replyText)) {
    echo json_encode(['success' => false, 'message' => 'Review ID and reply text are required']);
    exit;
}

// Get coach ID
$userId = $_SESSION['user']['id'];
$coachObj = new Coach((int)$userId);
$coachId = $coachObj->getCoachIdByUserId();

if (!$coachId) {
    echo json_encode(['success' => false, 'message' => 'Coach profile not found']);
    exit;
}

// Add reply
$reviewObj = new Review();
$success = $reviewObj->addReply($reviewId, $coachId, $replyText);

if ($success) {
    echo json_encode([
        'success' => true, 
        'message' => 'Reply added successfully',
        'reply' => [
            'text' => htmlspecialchars($replyText),
            'date' => 'Just now'
        ]
    ]);
} else {
    error_log("Failed to add reply - Review ID: $reviewId, Coach ID: $coachId");
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to add reply. Please check if the review belongs to you.',
        
    ]);
}

