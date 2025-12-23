<?php

declare(strict_types=1);

class Availability
{
    private PDO $db;
    private ?int $id = null;
    private ?int $coach_id = null;
    private string $date = '';
    private string $start_time = '';
    private string $end_time = '';
    private bool $is_available = true;

    public function __construct(?int $id = null)
    {
        $this->db = Database::getInstance();
        if ($id !== null) {
            $this->load($id);
        }
    }

    public function load(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM availabilities WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            $this->id = (int)$data['id'];
            $this->coach_id = (int)$data['coach_id'];
            $this->date = $data['date'] ?? '';
            $this->start_time = $data['start_time'] ?? '';
            $this->end_time = $data['end_time'] ?? '';
            $this->is_available = (bool)($data['is_available'] ?? true);
            return true;
        }
        return false;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCoachId(): ?int
    {
        return $this->coach_id;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getStartTime(): string
    {
        return $this->start_time;
    }
    public function getEndTime(): string
    {
        return $this->end_time;
    }
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    // Setters
    public function setCoachId(int $coachId): void
    {
        $this->coach_id = $coachId;
    }
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    public function setStartTime(string $startTime): void
    {
        $this->start_time = $startTime;
    }
    public function setEndTime(string $endTime): void
    {
        $this->end_time = $endTime;
    }
    public function setIsAvailable(bool $isAvailable): void
    {
        $this->is_available = $isAvailable;
    }

    
    public function getByCoach(int $coachId): array
    {
        $stmt = $this->db->prepare("
SELECT *
FROM availabilities
WHERE coach_id = ?
ORDER BY date ASC, start_time ASC
");
        $stmt->execute([$coachId]);
        return $stmt->fetchAll();
    }

    
    public function getById(int $availabilityId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM availabilities WHERE id = ?");
        $stmt->execute([$availabilityId]);
        return $stmt->fetch() ?: null;
    }

    
    public function create(int $coachId, string $date, string $startTime, string $endTime): bool
    {
        $stmt = $this->db->prepare("
INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available)
VALUES (?, ?, ?, ?, 1)
");
        return $stmt->execute([$coachId, $date, $startTime, $endTime]);
    }

   
    public function save(): bool
    {
        if ($this->id !== null) {
            $stmt = $this->db->prepare("
UPDATE availabilities
SET date = ?, start_time = ?, end_time = ?, is_available = ?
WHERE id = ?
");
            return $stmt->execute([$this->date, $this->start_time, $this->end_time, $this->is_available ? 1 : 0, $this->id]);
        } else {
            $stmt = $this->db->prepare("
INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available)
VALUES (?, ?, ?, ?, ?)
");
            $success = $stmt->execute([$this->coach_id, $this->date, $this->start_time, $this->end_time, $this->is_available ? 1 : 0]);
            if ($success) {
                $this->id = (int)$this->db->lastInsertId();
            }
            return $success;
        }
    }

   
    public function update(int $availabilityId, string $date, string $startTime, string $endTime, $isAvailable): bool
    {
        $stmt = $this->db->prepare("
UPDATE availabilities
SET date = ?, start_time = ?, end_time = ?, is_available = ?
WHERE id = ?
");
        return $stmt->execute([$date, $startTime, $endTime, (int)$isAvailable, $availabilityId]);
    }

    
    public function delete(?int $availabilityId = null): bool
    {
        $id = $availabilityId ?? $this->id;
        if (!$id) return false;

        $stmt = $this->db->prepare("DELETE FROM availabilities WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getRecurringSchedule(int $coachId): array
    {
        $stmt = $this->db->prepare("SELECT day_of_week, start_time, end_time FROM coach_recurring_slots WHERE coach_id = ?");
        $stmt->execute([$coachId]);

        $schedule = [
            'monday' => ['active' => false, 'slots' => []],
            'tuesday' => ['active' => false, 'slots' => []],
            'wednesday' => ['active' => false, 'slots' => []],
            'thursday' => ['active' => false, 'slots' => []],
            'friday' => ['active' => false, 'slots' => []],
            'saturday' => ['active' => false, 'slots' => []],
            'sunday' => ['active' => false, 'slots' => []],
        ];

        while ($row = $stmt->fetch()) {
            $day = (string)$row['day_of_week'];
            if (isset($schedule[$day])) {
                $schedule[$day]['active'] = true;
                $schedule[$day]['slots'][] = [
                    date('H:i', strtotime((string)$row['start_time'])),
                    date('H:i', strtotime((string)$row['end_time']))
                ];
            }
        }

        return $schedule;
    }

    public function saveCoachAvailability(int $coachId, array $schedule): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete existing recurring slots for this coach
            $stmt = $this->db->prepare("DELETE FROM coach_recurring_slots WHERE coach_id = ?");
            $stmt->execute([$coachId]);

            // Delete future availabilities for this coach
            $stmt = $this->db->prepare("DELETE FROM availabilities WHERE coach_id = ? AND date >= CURRENT_DATE");
            $stmt->execute([$coachId]);

            // Prepare insert statements
            $stmtRecurring = $this->db->prepare("INSERT INTO coach_recurring_slots (coach_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
            $stmtAvailability = $this->db->prepare("INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)");

            // Insert new recurring slots
            foreach ($schedule as $day => $data) {
                if (($data['active'] ?? false) && !empty($data['slots'])) {
                    foreach ($data['slots'] as $slot) {
                        $startTime = $slot[0];
                        $endTime = $slot[1];
                        $stmtRecurring->execute([$coachId, $day, $startTime, $endTime]);
                    }
                }
            }

            // Generate dates for next 30 days and insert into availabilities
            $begin = new DateTime();
            $end = new DateTime();
            $end->modify('+30 days');
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end);

            foreach ($daterange as $date) {
                $dayOfWeek = strtolower($date->format('l'));
                if (isset($schedule[$dayOfWeek]) && ($schedule[$dayOfWeek]['active'] ?? false) && !empty($schedule[$dayOfWeek]['slots'])) {
                    $dateString = $date->format('Y-m-d');
                    foreach ($schedule[$dayOfWeek]['slots'] as $slot) {
                        $startTime = $slot[0];
                        $endTime = $slot[1];
                        $stmtAvailability->execute([$coachId, $dateString, $startTime, $endTime]);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error saving coach availability in OOP: " . $e->getMessage());
            return false;
        }
    }
}
