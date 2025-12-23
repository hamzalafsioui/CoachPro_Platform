<?php
require_once '../../config/App.php';

$coachObj = new Coach();
$resObj = new Reservation();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}


$coachId = $coachObj->getCoachIdByUserId($_SESSION['user_id']);


if (!$coachId) {
    echo json_encode(['success' => false, 'message' => 'Coach profile not found']);
    exit;
}

$reservations = $resObj->getByCoach($coachId);

echo json_encode(['success' => true, 'data' => $reservations]);
