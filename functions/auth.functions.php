<?php
require_once __DIR__ . "/../config/database.php";


function registerUser($firstname, $lastname, $email, $phone, $password, $role = 'sportif')
{
    global $conn;

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'An account with this email already exists.',
                'user_id' => null
            ];
        }
        $stmt->close();

        // ==== (- FIX problem when the projet Run the first time without Insert Roles Manualy -) ======
        // Get role_id from roles table
        $stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $roleData = $result->fetch_assoc();
        $stmt->close();

        if (!$roleData) {
            // If role doesn't exist, create it
            $stmt = $conn->prepare("INSERT INTO roles (name) VALUES (?)");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $role_id = $conn->insert_id;
            $stmt->close();
        } else {
            $role_id = $roleData['id'];
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("
            INSERT INTO users (role_id, firstname, lastname, email, phone, password, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $phone_value = $phone ?: null;
        $stmt->bind_param(
            "isssss",
            $role_id,
            $firstname,
            $lastname,
            $email,
            $phone_value,
            $hashed_password
        );

        $stmt->execute();
        $user_id = $conn->insert_id;
        $stmt->close();

        // If the user is a coach, create a coach profile
        if ($role === 'coach') {
            $stmt = $conn->prepare("
                INSERT INTO coach_profiles (user_id, bio, experience_years, certifications, rating_avg) 
                VALUES (?, NULL, 0, NULL, 0.00)
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        return [
            'success' => true,
            'message' => 'Account created successfully! Please login to continue.',
            'user_id' => $user_id
        ];
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred during registration. Please try again.',
            'user_id' => null
        ];
    }
}


// login user
function loginUser($email, $password)
{
    global $conn;

    try {
    
        $stmt = $conn->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Set sessions
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];

            // set sessions user
            $_SESSION['user'] = [
                'id' => $user['id'],
                'role_id' => $user['role_id'],
                'role_name' => $user['role_name'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'email' => $user['email']
            ];

            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => $_SESSION['user']
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid email or password.',
            'user' => null
        ];
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred during login. Please try again.',
            'user' => null
        ];
    }
}
