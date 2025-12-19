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
