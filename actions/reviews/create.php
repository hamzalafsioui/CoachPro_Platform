<?php
require_once '../../config/App.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'sportif') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$reservationId = (int)($data['reservation_id'] ?? 0);
$rating = (int)($data['rating'] ?? 0);
$comment = trim($data['comment'] ?? '');
$authorId = $_SESSION['user']['id'];

if ($reservationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']);
    exit();
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit();
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment is required']);
    exit();
}

$reviewObj = new Review();

if ($reviewObj->hasReview($reservationId)) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this session']);
    exit();
}

$result = $reviewObj->create($reservationId, $authorId, $rating, $comment);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully'
    ]);
} else {
    error_log("Review creation failed - Reservation: $reservationId, Author: $authorId, Rating: $rating");
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit review. Please ensure the reservation is completed.',
        'debug' => [
            'reservation_id' => $reservationId,
            'author_id' => $authorId,
            'rating' => $rating
        ]
    ]);
}
