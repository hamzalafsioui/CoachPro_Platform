<?php
require_once __DIR__ . '/../config/database.php';

function getCoachProfile($userId)
{
    global $conn;

    $sql = "SELECT cp.*, u.firstname, u.lastname, u.email, u.phone 
            FROM users u
            LEFT JOIN coach_profiles cp ON u.id = cp.user_id 
            WHERE u.id = ? AND u.role_id = (SELECT id FROM roles WHERE name = 'coach')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

function getCoachProfileWithSports(int $coachId): ?array
{
    global $conn;

    $sql = "
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
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param("i", $coachId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Hardcoded icons for fallback
        $icons = ['fas fa-user-ninja', 'fas fa-leaf', 'fas fa-fist-raised', 'fas fa-apple-alt', 'fas fa-user-shield'];
        srand($row['id']);
        $icon = $icons[array_rand($icons)];
        srand();

        return [
            'id' => (int)$row['id'],
            'name' => $row['firstname'] . ' ' . $row['lastname'],
            'rating' => (float)$row['rating_avg'],
            'reviews_count' => (int)$row['review_count'],
            'hourly_rate' => '$50.00', // Hardcoded as not in DB yet
            'specialties' => $row['specialties'] ? explode(', ', $row['specialties']) : ['Training'],
            'bio' => $row['bio'] ?: 'No bio available.',
            'certifications' => $row['certifications'] ? explode(', ', $row['certifications']) : ['Certified Professional'],
            'image' => $icon
        ];
    }

    return null;
}

function getCoachIdByUserId(int $userId): ?int
{
    global $conn;

    $sql = "SELECT id FROM coach_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return (int)$row['id'];
    }

    return null;
}

function updateCoachProfile($userId, $firstname, $lastname, $email, $phone, $bio, $experience)
{
    global $conn;

    // update user data
    $userSql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($userSql);
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $phone, $userId);
    $stmt->execute();
    $stmt->close();

    // check if coach profile exists
    $checkSql = "SELECT id FROM coach_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Update coach profile
        $sql = "UPDATE coach_profiles SET bio = ?, experience_years = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $bio, $experience, $userId);
    } else {
        // Create new if not exist
        $sql = "INSERT INTO coach_profiles (user_id, bio, experience_years) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $userId, $bio, $experience);
    }

    return $stmt->execute();
}

function getAllCoachesWithDetails(): array
{
    global $conn;

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

    $result = $conn->query($sql);
    $coaches = [];

    if ($result) {
        // Hardcoded icons
        $icons = ['fas fa-user-ninja', 'fas fa-leaf', 'fas fa-fist-raised', 'fas fa-apple-alt', 'fas fa-user-shield'];

        while ($row = $result->fetch_assoc()) {
            srand($row['id']); // Consistent icon per coach
            $icon = $icons[array_rand($icons)];
            srand();

            $coaches[] = [
                'id' => (int)$row['id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'rating' => (float)$row['rating_avg'],
                'reviews' => (int)$row['review_count'],
                'specialties' => $row['specialties'] ? explode(', ', $row['specialties']) : ['Training'],
                'bio' => $row['bio'] ?: 'No bio available.',
                'image' => $icon // set image as icon
            ];
        }
    }

    return $coaches;
}

function getAllSports(): array
{
    global $conn;
    $sql = "SELECT * FROM sports ORDER BY name ASC";
    $result = $conn->query($sql);
    $sports = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sports[] = $row;
        }
    }
    return $sports;
}
