<?php

class Sportif extends User
{

    public function getStats(): array
    {
        $id = $this->getId();
        if (!$id) return [];

        $stats = [
            'workouts' => 0,
            'calories' => '4,250',
            'active_minutes' => 340
        ];

        $stmt = $this->db->prepare("
SELECT COUNT(*) as total
FROM reservations r
JOIN statuses s ON r.status_id = s.id
WHERE r.sportif_id = ? AND s.name IN ('confirmed', 'completed')
");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $stats['workouts'] = (int)$row['total'];
        }

        return $stats;
    }


    public function getNextSession(): ?array
    {
        $id = $this->getId();
        if (!$id) return null;

        $sql = "
SELECT
    r.id,
    u.firstname as coach_firstname,
    u.lastname as coach_lastname,
    a.date,
    a.start_time,
    a.end_time,
    s.name as status,
    GROUP_CONCAT(sp.name SEPARATOR ', ') as sports
FROM reservations r
JOIN availabilities a ON r.availability_id = a.id
JOIN coach_profiles cp ON r.coach_id = cp.id
JOIN users u ON cp.user_id = u.id
JOIN statuses s ON r.status_id = s.id
LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
LEFT JOIN sports sp ON sp.id = cs.sport_id
WHERE r.sportif_id = ?
AND (a.date > CURDATE() OR (a.date = CURDATE() AND a.start_time > CURTIME()))
AND s.name = 'confirmed'
GROUP BY r.id
ORDER BY a.date ASC, a.start_time ASC
LIMIT 1
";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
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

            return [
                'coach' => $row['coach_firstname'] . ' ' . $row['coach_lastname'],
                'type' => $row['sports'] ?: 'Personal Training',
                'date' => $displayDate,
                'time' => date('H:i', strtotime((string)$row['start_time'])) . ' - ' . date('H:i', strtotime((string)$row['end_time'])),
                'avatar' => strtoupper($row['coach_firstname'][0] . $row['coach_lastname'][0])
            ];
        }

        return null;
    }


    public function getRecentActivity(int $limit = 3): array
    {
        $id = $this->getId();
        if (!$id) return [];

        $sql = "
SELECT
r.id,
u.firstname as coach_firstname,
u.lastname as coach_lastname,
a.date,
s.name as status,
GROUP_CONCAT(sp.name SEPARATOR ', ') as sports
FROM reservations r
JOIN availabilities a ON r.availability_id = a.id
JOIN coach_profiles cp ON r.coach_id = cp.id
JOIN users u ON cp.user_id = u.id
JOIN statuses s ON r.status_id = s.id
LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
LEFT JOIN sports sp ON sp.id = cs.sport_id
WHERE r.sportif_id = ?
GROUP BY r.id
ORDER BY a.date DESC, a.start_time DESC
LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $activities = [];
        while ($row = $stmt->fetch()) {
            $timestamp = strtotime($row['date']);
            $today = strtotime(date('Y-m-d'));
            $diff = (int)floor(($today - $timestamp) / (60 * 60 * 24));

            if ($diff === 0) $displayDate = 'Today';
            elseif ($diff === 1) $displayDate = 'Yesterday';
            elseif ($diff < 7) $displayDate = $diff . ' days ago';
            else $displayDate = date('M j', $timestamp);

            $activities[] = [
                'title' => $row['sports'] ?: 'Workout',
                'date' => $displayDate,
                'coach' => $row['coach_firstname'] . ' ' . $row['coach_lastname']
            ];
        }

        return $activities;
    }

    public function getWeeklyActivity(): array
    {
        return [
            ['day' => 'M', 'height' => '40%'],
            ['day' => 'T', 'height' => '70%'],
            ['day' => 'W', 'height' => '30%'],
            ['day' => 'T', 'height' => '85%'],
            ['day' => 'F', 'height' => '60%'],
            ['day' => 'S', 'height' => '90%'],
            ['day' => 'S', 'height' => '20%'],
        ];
    }

    public function deleteAccount(): bool
    {
        $id = $this->getId();
        if (!$id) return false;

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
