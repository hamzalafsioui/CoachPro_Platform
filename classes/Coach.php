<?php

declare(strict_types=1);

class Coach extends User
{
    private ?int $coach_id = null;
    private string $bio = '';
    private int $experience_years = 0;
    private string $certifications = '';
    private float $rating_avg = 0.0;
    private ?string $photo = null;
    private float $hourly_rate = 50.00;

    // Override 
    public function load(int $userId): bool
    {
        if (parent::load($userId)) {
            $stmt = $this->db->prepare("SELECT * FROM coach_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch();

            if ($data) {
                $this->coach_id = (int)$data['id'];
                $this->bio = $data['bio'] ?? '';
                $this->experience_years = (int)($data['experience_years'] ?? 0);
                $this->certifications = $data['certifications'] ?? '';
                $this->rating_avg = (float)($data['rating_avg'] ?? 0.0);
                $this->photo = $data['photo'] ?? null;
                $this->hourly_rate = (float)($data['hourly_rate'] ?? 50.00);
            }
            return true;
        }
        return false;
    }

    // Getters
    public function getCoachId(): ?int
    {
        return $this->coach_id;
    }
    public function getBio(): string
    {
        return $this->bio;
    }
    public function getExperienceYears(): int
    {
        return $this->experience_years;
    }
    public function getCertifications(): string
    {
        return $this->certifications;
    }
    public function getRatingAvg(): float
    {
        return $this->rating_avg;
    }
    public function getPhoto(): ?string
    {
        return $this->photo;
    }
    public function getHourlyRate(): float
    {
        return $this->hourly_rate;
    }

    // Setters
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }
    public function setExperienceYears(int $years): void
    {
        $this->experience_years = $years;
    }
    public function setCertifications(string $cert): void
    {
        $this->certifications = $cert;
    }
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }
    public function setHourlyRate(float $rate): void
    {
        $this->hourly_rate = $rate;
    }

    public function getProfile(?int $userId = null): ?array
    {
        $id = $userId ?? $this->getId();
        if (!$id) return null;

        $stmt = $this->db->prepare("
SELECT cp.*, u.firstname, u.lastname, u.email, u.phone
FROM users u
LEFT JOIN coach_profiles cp ON u.id = cp.user_id
WHERE u.id = ? AND u.role_id = (SELECT id FROM roles WHERE name = 'coach')
");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }


    public function getProfileWithSports(?int $coachId = null): ?array
    {
        $id = $coachId ?? $this->coach_id;
        if (!$id) return null;

        $stmt = $this->db->prepare("
SELECT
cp.id,
u.firstname,
u.lastname,
u.email,
cp.bio,
cp.experience_years,
cp.certifications,
cp.rating_avg,
cp.photo,
cp.hourly_rate,
GROUP_CONCAT(s.name SEPARATOR ', ') as specialties,
(SELECT COUNT(*) FROM reviews r JOIN reservations res ON r.reservation_id = res.id WHERE res.coach_id = cp.id) as review_count
FROM coach_profiles cp
JOIN users u ON cp.user_id = u.id
LEFT JOIN coach_sports cs ON cp.id = cs.coach_id
LEFT JOIN sports s ON cs.sport_id = s.id
WHERE cp.id = ?
GROUP BY cp.id
");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            $icons = ['fas fa-user-ninja', 'fas fa-leaf', 'fas fa-fist-raised', 'fas fa-apple-alt', 'fas fa-user-shield'];
            srand((int)$row['id']);
            $icon = $icons[array_rand($icons)];
            srand();

            return [
                'id' => (int)$row['id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'rating' => (float)$row['rating_avg'],
                'reviews_count' => (int)$row['review_count'],
                'hourly_rate' => '$' . number_format((float)($row['hourly_rate'] ?? 50.00), 2),
                'specialties' => $row['specialties'] ? explode(', ', $row['specialties']) : ['Training'],
                'bio' => $row['bio'] ?: 'No bio available.',
                'certifications' => $row['certifications'] ? explode(', ', $row['certifications']) : ['Certified Professional'],
                'image' => $icon
            ];
        }

        return null;
    }


    public function getCoachIdByUserId(?int $userId = null): ?int
    {
        $id = $userId ?? $this->getId();
        if (!$id) return null;

        $stmt = $this->db->prepare("SELECT id FROM coach_profiles WHERE user_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }

    /**
     * Get coach specialties/sports
     */
    public function getSpecialties(?int $coachId = null): array
    {
        $id = $coachId ?? $this->coach_id;
        if (!$id) return [];

        $stmt = $this->db->prepare("
            SELECT s.name 
            FROM sports s
            JOIN coach_sports cs ON s.id = cs.sport_id
            WHERE cs.coach_id = ?
            ORDER BY s.name
        ");
        $stmt->execute([$id]);
        
        $specialties = [];
        while ($row = $stmt->fetch()) {
            $specialties[] = $row['name'];
        }
        
        return $specialties;
    }


    public function updateProfile(?int $userId = null, ?array $data = null): bool
    {
        if ($data === null) {
            $data = (array)$userId;
            $id = $this->getId();
        } else {
            $id = $userId ?? $this->getId();
        }

        if (!$id) return false;

        try {
            $this->db->beginTransaction();

            // First update user 
            $userData = [
                'firstname' => $data['firstname'] ?? '',
                'lastname' => $data['lastname'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null
            ];

            if ($id === $this->getId()) {
                if (!$this->update($userData)) {
                    throw new Exception('User update failed');
                }
            } else {
                $userObj = new User($id);
                $userObj->update($userData);
            }

            // Then update or insert coach_profiles
            $stmt = $this->db->prepare("SELECT id FROM coach_profiles WHERE user_id = ?");
            $stmt->execute([$id]);
            $exists = $stmt->fetch();

            if ($exists) {
                $stmt = $this->db->prepare("UPDATE coach_profiles SET bio = ?, experience_years = ?, hourly_rate = ? WHERE user_id = ?");
                $success = $stmt->execute([$data['bio'] ?? '', $data['experience'] ?? 0, $data['hourly_rate'] ?? 50.00, $id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO coach_profiles (user_id, bio, experience_years, hourly_rate) VALUES (?, ?, ?, ?)");
                $success = $stmt->execute([$id, $data['bio'] ?? '', $data['experience'] ?? 0, $data['hourly_rate'] ?? 50.00]);
            }

            if (!$success) {
                throw new Exception("Failed to update coach profile");
            }

            $this->db->commit();

            if ($id === $this->getId()) {
                $this->load($id);
            }

            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }


    public function getAllDetailed(): array
    {
        $sql = "
SELECT
cp.id,
u.firstname,
u.lastname,
u.email,
cp.bio,
cp.experience_years,
cp.rating_avg,
cp.photo,
GROUP_CONCAT(s.name SEPARATOR ', ') as specialties,
(SELECT COUNT(*) FROM reviews r JOIN reservations res ON r.reservation_id = res.id WHERE res.coach_id = cp.id) as review_count
FROM coach_profiles cp
JOIN users u ON cp.user_id = u.id
LEFT JOIN coach_sports cs ON cp.id = cs.coach_id
LEFT JOIN sports s ON cs.sport_id = s.id
GROUP BY cp.id
";

        $stmt = $this->db->query($sql);
        $coaches = [];
        $icons = ['fas fa-user-ninja', 'fas fa-leaf', 'fas fa-fist-raised', 'fas fa-apple-alt', 'fas fa-user-shield'];

        while ($row = $stmt->fetch()) {
            srand((int)$row['id']);
            $icon = $icons[array_rand($icons)];
            srand();

            $coaches[] = [
                'id' => (int)$row['id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'rating' => (float)$row['rating_avg'],
                'reviews' => (int)$row['review_count'],
                'specialties' => $row['specialties'] ? explode(', ', $row['specialties']) : ['Training'],
                'bio' => $row['bio'] ?: 'No bio available.',
                'image' => $icon
            ];
        }

        return $coaches;
    }


    public function getClients(): array
    {
        if (!$this->coach_id) return [];

        // Try to get clients from coach_clients table first (new way)
        $sql = "
            SELECT 
                u.id, 
                u.firstname, 
                u.lastname, 
                u.email,
                cc.status,
                cc.progress,
                cc.joined_at,
                cp.name as plan_name,
                MAX(a.date) as last_session_date
            FROM users u
            INNER JOIN coach_clients cc ON u.id = cc.sportif_id AND cc.coach_id = ?
            LEFT JOIN client_plans cp ON cc.plan_id = cp.id
            LEFT JOIN reservations r ON u.id = r.sportif_id AND r.coach_id = cc.coach_id
            LEFT JOIN availabilities a ON r.availability_id = a.id
            GROUP BY u.id, cc.status, cc.progress, cc.joined_at, cp.name
            ORDER BY last_session_date DESC, cc.joined_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->coach_id]);
        $rows = $stmt->fetchAll();

        // If no coach_clients records exist, fall back to getting from reservations
        if (empty($rows)) {
            $sql = "
                SELECT DISTINCT
                    u.id, 
                    u.firstname, 
                    u.lastname, 
                    u.email,
                    MAX(a.date) as last_session_date,
                    MIN(r.created_at) as first_reservation
                FROM users u
                JOIN reservations r ON u.id = r.sportif_id
                LEFT JOIN availabilities a ON r.availability_id = a.id
                WHERE r.coach_id = ?
                GROUP BY u.id
                ORDER BY last_session_date DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->coach_id]);
            $rows = $stmt->fetchAll();
        }

        $clients = [];

        foreach ($rows as $row) {
            // Format last session date
            $lastSessionDisplay = 'No sessions yet';
            if ($row['last_session_date']) {
                $lastSessionTime = strtotime((string)$row['last_session_date']);
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
            }

            // Format join date - use joined_at from coach_clients, or first_reservation as fallback
            $joinDate = 'N/A';
            if (!empty($row['joined_at'])) {
                $joinDate = date('M j, Y', strtotime((string)$row['joined_at']));
            } elseif (!empty($row['first_reservation'])) {
                $joinDate = date('M j, Y', strtotime((string)$row['first_reservation']));
            }

            $clients[] = [
                'id' => (int)$row['id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'avatar' => strtoupper(substr((string)$row['firstname'], 0, 1) . substr((string)$row['lastname'], 0, 1)),
                'status' => $row['status'] ?? 'active',
                'plan' => $row['plan_name'] ?? 'No plan assigned',
                'join_date' => $joinDate,
                'progress' => (int)($row['progress'] ?? 0),
                'last_session' => $lastSessionDisplay,
                'email' => $row['email']
            ];
        }

        return $clients;
    }
}
