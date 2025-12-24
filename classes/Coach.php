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
                'hourly_rate' => '$50.00',
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


    public function updateProfile(?int $userId = null, ?array $data = null): bool
    {
        if ($data === null) {
            $data = (array)$userId;
            $id = $this->getId();
        } else {
            $id = $userId;
        }

        if (!$id) return false;

        // First update user 
        $userData = [
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null
        ];
        $this->update($userData);

        // Then update or insert coach_profiles
        $stmt = $this->db->prepare("SELECT id FROM coach_profiles WHERE user_id = ?");
        $stmt->execute([$id]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $this->db->prepare("UPDATE coach_profiles SET bio = ?, experience_years = ? WHERE user_id = ?");
            $success = $stmt->execute([$data['bio'] ?? '', $data['experience'] ?? 0, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO coach_profiles (user_id, bio, experience_years) VALUES (?, ?, ?)");
            $success = $stmt->execute([$id, $data['bio'] ?? '', $data['experience'] ?? 0]);
        }

        if ($success && $id === $this->getId()) {
            $this->load($id);
        }

        return $success;
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

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->coach_id]);

        $clients = [];
        $plans = ['Premium - Personal Training', 'Standard - HIIT', 'Basic - Strength', 'Premium - Cardio'];
        $statuses = ['active', 'inactive'];

        while ($row = $stmt->fetch()) {
            srand((int)$row['id']);
            $plan = $plans[array_rand($plans)];
            $progress = rand(10, 100);
            $status = $statuses[rand(0, 1)];
            srand();

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

            $clients[] = [
                'id' => (int)$row['id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'avatar' => strtoupper(substr((string)$row['firstname'], 0, 1) . substr((string)$row['lastname'], 0, 1)),
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
}
