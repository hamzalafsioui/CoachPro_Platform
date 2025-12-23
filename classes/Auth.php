<?php

class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    
    public function register($firstname, $lastname, $email, $phone, $password, $role = 'sportif')
    {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'An account with this email already exists.',
                    'user_id' => null
                ];
            }

            // Get role_id from roles table
            $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$role]);
            $roleData = $stmt->fetch();

            if (!$roleData) {
                // If role doesn't exist, create it
                $stmt = $this->db->prepare("INSERT INTO roles (name) VALUES (?)");
                $stmt->execute([$role]);
                $role_id = $this->db->lastInsertId();
            } else {
                $role_id = $roleData['id'];
            }

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $this->db->prepare("
                INSERT INTO users (role_id, firstname, lastname, email, phone, password, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $phone_value = $phone ?: null;
            $stmt->execute([
                $role_id,
                $firstname,
                $lastname,
                $email,
                $phone_value,
                $hashed_password
            ]);

            $user_id = $this->db->lastInsertId();

            // If the user is a coach, create a coach profile
            if ($role === 'coach') {
                $stmt = $this->db->prepare("
                    INSERT INTO coach_profiles (user_id, bio, experience_years, certifications, rating_avg) 
                    VALUES (?, NULL, 0, NULL, 0.00)
                ");
                $stmt->execute([$user_id]);
            }

            return [
                'success' => true,
                'message' => 'Account created successfully! Please login to continue.',
                'user_id' => $user_id
            ];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during registration. Please try again.',
                'user_id' => null
            ];
        }
    }

    
    public function login($email, $password)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Determine which class to instantiate
                $role = $user['role_name'];
                $userObj = null;

                if ($role === 'coach') {
                    $userObj = new Coach($user['id']);
                } elseif ($role === 'sportif') {
                    $userObj = new Sportif($user['id']);
                } else {
                    $userObj = new User($user['id']);
                }

                // Set sessions
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $role;
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'role_id' => $user['role_id'],
                    'role_name' => $role,
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'email' => $user['email']
                ];

                return [
                    'success' => true,
                    'message' => 'Login successful!',
                    'user' => $_SESSION['user'],
                    'object' => $userObj
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid email or password.',
                'user' => null,
                'object' => null
            ];
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'user' => null,
                'object' => null
            ];
        }
    }

   
    public static function logout()
    {
        session_unset();
        session_destroy();
    }
}
