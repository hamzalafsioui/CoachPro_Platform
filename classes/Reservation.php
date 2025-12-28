<?php

declare(strict_types=1);

class Reservation
{
    private PDO $db;
    private ?int $id = null;
    private ?int $sportif_id = null;
    private ?int $coach_id = null;
    private ?int $availability_id = null;
    private ?int $status_id = null;
    private float $price = 0.0;
    private ?string $created_at = null;

    public function __construct(?int $id = null)
    {
        $this->db = Database::getInstance();
        if ($id !== null) {
            $this->load($id);
        }
    }

    public function load(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            $this->id = (int)$data['id'];
            $this->sportif_id = (int)$data['sportif_id'];
            $this->coach_id = (int)$data['coach_id'];
            $this->availability_id = (int)$data['availability_id'];
            $this->status_id = (int)$data['status_id'];
            $this->price = (float)$data['price'];
            $this->created_at = $data['created_at'];
            return true;
        }
        return false;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getSportifId(): ?int
    {
        return $this->sportif_id;
    }
    public function getCoachId(): ?int
    {
        return $this->coach_id;
    }
    public function getAvailabilityId(): ?int
    {
        return $this->availability_id;
    }
    public function getStatusId(): ?int
    {
        return $this->status_id;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    // Setters
    public function setSportifId(int $id): void
    {
        $this->sportif_id = $id;
    }
    public function setCoachId(int $id): void
    {
        $this->coach_id = $id;
    }
    public function setAvailabilityId(int $id): void
    {
        $this->availability_id = $id;
    }
    public function setStatusId(int $id): void
    {
        $this->status_id = $id;
    }
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

   
    public function updateStatus(string $statusName, ?int $reservationId = null): bool
    {
        $id = $reservationId ?? $this->id;
        if (!$id) return false;

        $stmt = $this->db->prepare("SELECT id FROM statuses WHERE name = ?");
        $stmt->execute([$statusName]);
        $row = $stmt->fetch();

        if (!$row) return false;

        $statusId = (int)$row['id'];

        $stmt = $this->db->prepare("UPDATE reservations SET status_id = ? WHERE id = ?");
        $success = $stmt->execute([$statusId, $id]);

        if ($success && $id === $this->id) {
            $this->status_id = $statusId;
        }

        return $success;
    }

   
    public function delete(?int $reservationId = null): bool
    {
        $id = $reservationId ?? $this->id;
        if (!$id) return false;

        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = ?");
        return $stmt->execute([$id]);
    }

 
    public function create(int $sportifId, int $coachId, int $availabilityId, float $price)
    {
        try {
            $this->db->beginTransaction();

            // Check if availability is still available
            $stmt = $this->db->prepare("SELECT is_available FROM availabilities WHERE id = ? AND is_available = 1 FOR UPDATE");
            $stmt->execute([$availabilityId]);
            $avail = $stmt->fetch();

            if (!$avail) {
                $this->db->rollBack();
                return false;
            }

            // Get status ID for 'pending'
            $stmt = $this->db->prepare("SELECT id FROM statuses WHERE name = 'pending'");
            $stmt->execute();
            $statusRow = $stmt->fetch();

            if (!$statusRow) {
                $this->db->rollBack();
                return false;
            }

            $statusId = (int)$statusRow['id'];

            // Create reservation
            $stmt = $this->db->prepare("
INSERT INTO reservations
(sportif_id, coach_id, availability_id, status_id, price)
VALUES (?, ?, ?, ?, ?)
");
            $stmt->execute([$sportifId, $coachId, $availabilityId, $statusId, $price]);
            $reservationId = (int)$this->db->lastInsertId();

            // Update availability to consumed (is ticked by another one)
            $stmt = $this->db->prepare("UPDATE availabilities SET is_available = 0 WHERE id = ?");
            $stmt->execute([$availabilityId]);

            $this->db->commit();

            if ($this->id === null) {
                $this->load($reservationId);
            }

            return $reservationId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Reservation creation error: " . $e->getMessage());
            return false;
        }
    }

   
    public function getCoachUpcomingSessions(int $coachId, int $limit = 3): array
    {
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
LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);

        $sessions = [];
        while ($row = $stmt->fetch()) {
            $timestamp = strtotime((string)$row['date']);
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
                'type' => $row['sports'] ?: 'Training',
                'date' => $displayDate,
                'time' => date('H:i', strtotime((string)$row['start_time'])),
                'status' => ucfirst((string)$row['status_name'])
            ];
        }

        return $sessions;
    }

   
    public function getByCoach(int $coachId): array
    {
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

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$coachId]);

        $reservations = [];
        while ($row = $stmt->fetch()) {
            $reservations[] = [
                'id' => (int)$row['id'],
                'client' => $row['firstname'] . ' ' . $row['lastname'],
                'avatar' => strtoupper($row['firstname'][0] . $row['lastname'][0]),
                'type' => $row['sports'] ?: 'Training',
                'date' => $row['date'],
                'time' => date('H:i', strtotime((string)$row['start_time'])) . ' - ' . date('H:i', strtotime((string)$row['end_time'])),
                'status' => $row['status_name'],
                'price' => '$' . number_format((float)$row['price'], 2)
            ];
        }

        return $reservations;
    }

   
    public function getBySportif(int $sportifId): array
    {
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
GROUP_CONCAT(sp.name SEPARATOR ', ') AS sports,
(SELECT COUNT(*) FROM reviews WHERE reservation_id = r.id) as has_review
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

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sportifId]);

        $reservations = [];
        while ($row = $stmt->fetch()) {
            $reservations[] = [
                'id' => (int)$row['id'],
                'coach' => $row['firstname'] . ' ' . $row['lastname'],
                'avatar' => strtoupper($row['firstname'][0] . $row['lastname'][0]),
                'type' => $row['sports'] ?: 'Training',
                'date' => $row['date'],
                'time' => date('H:i', strtotime((string)$row['start_time'])) . ' - ' . date('H:i', strtotime((string)$row['end_time'])),
                'status' => $row['status_name'],
                'price' => '$' . number_format((float)$row['price'], 2),
                'has_review' => (int)$row['has_review'] > 0
            ];
        }

        return $reservations;
    }
}
