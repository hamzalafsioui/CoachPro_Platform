<?php
require_once __DIR__ . '/../config/database.php';


function getCoachAvailabilities($coachId)
{
    global $conn;

    $sql = "
        SELECT *
        FROM availabilities
        WHERE coach_id = ?
        ORDER BY date ASC, start_time ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $coachId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


function getAvailabilityById($availabilityId)
{
    global $conn;

    $sql = "SELECT * FROM availabilities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $availabilityId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}


function createAvailability($coachId, $date, $startTime, $endTime)
{
    global $conn;

    $sql = "
        INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available)
        VALUES (?, ?, ?, ?, 1)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $coachId, $date, $startTime, $endTime);

    return $stmt->execute();
}



function updateAvailability($availabilityId, $date, $startTime, $endTime, $isAvailable)
{
    global $conn;

    $sql = "
        UPDATE availabilities
        SET date = ?, start_time = ?, end_time = ?, is_available = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $date, $startTime, $endTime, $isAvailable, $availabilityId);

    return $stmt->execute();
}



function deleteAvailability($availabilityId)
{
    global $conn;

    $sql = "DELETE FROM availabilities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $availabilityId);

    return $stmt->execute();
}

function getCoachRecurringSchedule(int $coachId): array
{
    global $conn;

    $sql = "SELECT day_of_week, start_time, end_time FROM coach_recurring_slots WHERE coach_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $coachId);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedule = [
        'monday' => ['active' => false, 'slots' => []],
        'tuesday' => ['active' => false, 'slots' => []],
        'wednesday' => ['active' => false, 'slots' => []],
        'thursday' => ['active' => false, 'slots' => []],
        'friday' => ['active' => false, 'slots' => []],
        'saturday' => ['active' => false, 'slots' => []],
        'sunday' => ['active' => false, 'slots' => []],
    ];

    while ($row = $result->fetch_assoc()) {
        $day = $row['day_of_week'];
        $schedule[$day]['active'] = true;
        $schedule[$day]['slots'][] = [
            date('H:i', strtotime($row['start_time'])),
            date('H:i', strtotime($row['end_time']))
        ];
    }

    return $schedule;
}

function saveCoachAvailability(int $coachId, array $schedule): bool
{
    global $conn;

    $conn->begin_transaction();

    try {
        // Delete existing recurring slots for this coach
        $deleteSql = "DELETE FROM coach_recurring_slots WHERE coach_id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $coachId);
        $stmt->execute();

        // Insert new slots
        $insertSql = "INSERT INTO coach_recurring_slots (coach_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);

        foreach ($schedule as $day => $data) {
            if ($data['active'] && !empty($data['slots'])) {
                foreach ($data['slots'] as $slot) {
                    $startTime = $slot[0];
                    $endTime = $slot[1];
                    $stmt->bind_param("isss", $coachId, $day, $startTime, $endTime);
                    $stmt->execute();
                }
            }
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error saving coach availability: " . $e->getMessage());
        return false;
    }
}
