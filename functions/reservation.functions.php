<?php
require_once __DIR__ . '/../config/database.php';


function updateReservationStatus(int $reservationId, string $statusName): bool
{
    global $conn;

    $statusStmt = $conn->prepare("SELECT id FROM statuses WHERE name = ?");
    $statusStmt->bind_param("s", $statusName);
    $statusStmt->execute();
    $statusResult = $statusStmt->get_result();

    if (!$statusRow = $statusResult->fetch_assoc()) {
        return false;
    }

    $statusId = $statusRow['id'];

    $stmt = $conn->prepare(
        "UPDATE reservations SET status_id = ? WHERE id = ?"
    );
    $stmt->bind_param("ii", $statusId, $reservationId);

    return $stmt->execute();
}
function deleteReservation(int $reservationId): bool
{
    global $conn;

    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $reservationId);

    return $stmt->execute();
}
function createReservation(int $sportifId, int $coachId, int $availabilityId, float $price)
{
    global $conn;

    // begin transaction
    $conn->begin_transaction();

    try {
        // Check if availability is still available
        $availStmt = $conn->prepare("SELECT is_available FROM availabilities WHERE id = ? AND is_available = 1 FOR UPDATE");
        $availStmt->bind_param("i", $availabilityId);
        $availStmt->execute();
        $availResult = $availStmt->get_result();

        if ($availResult->num_rows === 0) {
            $conn->rollback();
            return false;
        }

        // Get status ID for 'pending'
        $statusStmt = $conn->prepare("SELECT id FROM statuses WHERE name = 'pending'");
        $statusStmt->execute();
        $statusResult = $statusStmt->get_result();

        if (!$statusRow = $statusResult->fetch_assoc()) {
            $conn->rollback();
            return false;
        }

        $statusId = $statusRow['id'];

        // Create reservation
        $stmt = $conn->prepare("
            INSERT INTO reservations
            (sportif_id, coach_id, availability_id, status_id, price)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("iiiid", $sportifId, $coachId, $availabilityId, $statusId, $price);
        $stmt->execute();
        $reservationId = $conn->insert_id;

        // Update availability to consumed
        $updateAvail = $conn->prepare("UPDATE availabilities SET is_available = 0 WHERE id = ?");
        $updateAvail->bind_param("i", $availabilityId);
        $updateAvail->execute();

        $conn->commit();
        return $reservationId;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Reservation error: " . $e->getMessage());
        return false;
    }
}

function getCoachUpcomingSessions(int $coachId, int $limit = 3): array
{
    global $conn;

    $sql = "
        SELECT
            r.id,
            u.firstname,
            u.lastname,
            a.date,
            a.start_time,
            a.end_time,
            s.name AS status_name,
            GROUP_CONCAT(sp.name SEPARATOR ', ') AS sports
        FROM reservations r
        JOIN availabilities a ON r.availability_id = a.id
        JOIN users u ON r.sportif_id = u.id
        JOIN statuses s ON r.status_id = s.id
        LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
        LEFT JOIN sports sp ON sp.id = cs.sport_id
        WHERE r.coach_id = ?
          AND (
              a.date > CURDATE()
              OR (a.date = CURDATE() AND a.start_time > CURTIME())
          )
        GROUP BY r.id
        ORDER BY a.date ASC, a.start_time ASC
        LIMIT ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("ii", $coachId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $sessions = [];

    while ($row = $result->fetch_assoc()) {

        // Date formatting
        $timestamp = strtotime($row['date']);
        $today = strtotime(date('Y-m-d'));
        $tomorrow = strtotime('+1 day', $today);

        if ($timestamp === $today) {
            $displayDate = 'Today';
        } elseif ($timestamp === $tomorrow) {
            $displayDate = 'Tomorrow';
        } else {
            $displayDate = date('M j', $timestamp);
        }

        $sessions[] = [
            'client' => $row['firstname'] . ' ' . $row['lastname'],
            'type'   => $row['sports'] ?: 'Training',
            'date'   => $displayDate,
            'time'   => date('H:i', strtotime($row['start_time'])),
            'status' => ucfirst($row['status_name'])
        ];
    }

    return $sessions;
}
function getCoachReservations(int $coachId): array
{
    global $conn;

    $sql = "
        SELECT
            r.id,
            r.price,
            u.firstname,
            u.lastname,
            a.date,
            a.start_time,
            a.end_time,
            s.name AS status_name,
            GROUP_CONCAT(sp.name SEPARATOR ', ') AS sports
        FROM reservations r
        JOIN users u ON r.sportif_id = u.id
        JOIN availabilities a ON r.availability_id = a.id
        JOIN statuses s ON r.status_id = s.id
        LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
        LEFT JOIN sports sp ON sp.id = cs.sport_id
        WHERE r.coach_id = ?
        GROUP BY r.id
        ORDER BY a.date DESC, a.start_time DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $coachId);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservations = [];

    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'id'     => $row['id'],
            'client' => $row['firstname'] . ' ' . $row['lastname'],
            'avatar' => strtoupper($row['firstname'][0] . $row['lastname'][0]),
            'type'   => $row['sports'] ?: 'Training',
            'date'   => $row['date'],
            'time'   => date('H:i', strtotime($row['start_time'])) . ' - ' .
                date('H:i', strtotime($row['end_time'])),
            'status' => $row['status_name'],
            'price'  => '$' . number_format($row['price'], 2)
        ];
    }

    return $reservations;
}

function getSportifReservations(int $sportifId): array
{
    global $conn;

    $sql = "
        SELECT
            r.id,
            r.price,
            u.firstname,
            u.lastname,
            a.date,
            a.start_time,
            a.end_time,
            s.name AS status_name,
            GROUP_CONCAT(sp.name SEPARATOR ', ') AS sports
        FROM reservations r
        JOIN coach_profiles cp ON r.coach_id = cp.id
        JOIN users u ON cp.user_id = u.id
        JOIN availabilities a ON r.availability_id = a.id
        JOIN statuses s ON r.status_id = s.id
        LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
        LEFT JOIN sports sp ON sp.id = cs.sport_id
        WHERE r.sportif_id = ?
        GROUP BY r.id
        ORDER BY a.date DESC, a.start_time DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $sportifId);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservations = [];

    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'id'     => $row['id'],
            'coach'  => $row['firstname'] . ' ' . $row['lastname'],
            'avatar' => strtoupper($row['firstname'][0] . $row['lastname'][0]),
            'type'   => $row['sports'] ?: 'Training',
            'date'   => $row['date'],
            'time'   => date('H:i', strtotime($row['start_time'])) . ' - ' .
                date('H:i', strtotime($row['end_time'])),
            'status' => $row['status_name'],
            'price'  => '$' . number_format($row['price'], 2)
        ];
    }

    return $reservations;
}
