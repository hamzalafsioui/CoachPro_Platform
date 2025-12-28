<?php
require_once '../../config/App.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $coachObj = new Coach((int)$userId);

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $experience = (int)($_POST['experience'] ?? 0);
    $hourly_rate = (float)($_POST['hourly_rate'] ?? 50.00);

    $nameParts = explode(' ', $name, 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';

    $data = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'phone' => $phone,
        'bio' => $bio,
        'experience' => $experience,
        'hourly_rate' => $hourly_rate
    ];

    $result = $coachObj->updateProfile(null, $data);

    if ($result) {
        // Update session data
        $_SESSION['user']['firstname'] = $firstname;
        $_SESSION['user']['lastname'] = $lastname;

        session_write_close();
        header("Location: ../../pages/coach/profile.php?status=success");
    } else {
        session_write_close();
        header("Location: ../../pages/coach/profile.php?status=error");
    }
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
