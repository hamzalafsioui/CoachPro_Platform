<?php
require_once __DIR__ . '/../config/database.php';

function getCoachClients(int $coachId): array
{
    global $conn;

    
    $sql = "
        SELECT 
            u.id, 
            u.firstname, 
            u.lastname, 
            u.email,
            MAX(a.date) as last_session_date
        FROM users u
        JOIN reservations r ON u.id = r.sportif_id
        JOIN availabilities a ON r.availability_id = a.id
        WHERE r.coach_id = ?
        GROUP BY u.id
        ORDER BY last_session_date DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $coachId);
    $stmt->execute();
    $result = $stmt->get_result();

    $clients = [];

    // Hardcoded 
    $plans = ['Premium - Personal Training', 'Standard - HIIT', 'Basic - Strength', 'Premium - Cardio'];
    $statuses = ['active', 'inactive'];

    while ($row = $result->fetch_assoc()) {
        
        // seed with user id (- to keep it consistent across reloads -)
        srand($row['id']);

        $plan = $plans[array_rand($plans)];
        $progress = rand(10, 100);
        $status = $statuses[rand(0, 1)];

        // Reset random seed
        srand();

        // Format last session
        $lastSessionTime = strtotime($row['last_session_date']);
        $today = strtotime(date('Y-m-d'));
        $diffDays = floor(($today - $lastSessionTime) / (60 * 60 * 24));

        if ($diffDays == 0) {
            $lastSessionDisplay = 'Today';
        } elseif ($diffDays == 1) {
            $lastSessionDisplay = 'Yesterday';
        } elseif ($diffDays < 30) {
            $lastSessionDisplay = $diffDays . ' days ago';
        } else {
            $lastSessionDisplay = date('M j, Y', $lastSessionTime);
        }

        $clients[] = [
            'id' => $row['id'],
            'name' => $row['firstname'] . ' ' . $row['lastname'],
            'avatar' => strtoupper(substr($row['firstname'], 0, 1) . substr($row['lastname'], 0, 1)),
            'status' => $status,
            'plan' => $plan,
            'join_date' => date('M j, Y', strtotime('-' . rand(1, 120) . ' days')),
            'progress' => $progress,
            'last_session' => $lastSessionDisplay,
            'email' => $row['email']
        ];
    }

    return $clients;
}
