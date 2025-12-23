<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class User
{
    protected PDO $db;
    private ?int $id = null;
    private string $firstname = '';
    private string $lastname = '';
    private string $email = '';
    private ?string $phone = null;
    private int $role_id = 0;
    private ?string $created_at = null;
    private ?string $updated_at = null;

    public function __construct(?int $id = null)
    {
        $this->db = Database::getInstance();
        if ($id !== null) {
            $this->load($id);
        }
    }

    public static function find(int $id): ?self
    {
        $user = new self();
        if ($user->load($id)) {
            return $user;
        }
        return null;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function load(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            $this->id = (int)$data['id'];
            $this->firstname = $data['firstname'];
            $this->lastname = $data['lastname'];
            $this->email = $data['email'];
            $this->phone = $data['phone'];
            $this->role_id = (int)$data['role_id'];
            $this->created_at = $data['created_at'];
            $this->updated_at = $data['updated_at'];
            return true;
        }
        return false;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function getRoleId(): int
    {
        return $this->role_id;
    }
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    // Setters
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }


    public function update(?array $data = null): bool
    {
        if ($this->id === null) return false;

        if ($data !== null) {
            $this->firstname = $data['firstname'] ?? $this->firstname;
            $this->lastname = $data['lastname'] ?? $this->lastname;
            $this->email = $data['email'] ?? $this->email;
            $this->phone = $data['phone'] ?? $this->phone;
            $this->role_id = (int)($data['role_id'] ?? $this->role_id);
        }

        $sql = "UPDATE users SET
            firstname = ?,
            lastname = ?,
            email = ?,
            phone = ?,
            role_id = ?,
            updated_at = NOW()
            WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->phone,
            $this->role_id,
            $this->id
        ]);

        if ($success) {
            $this->load($this->id);
        }

        return $success;
    }

    public function verifyPassword(string $password): bool
    {
        if ($this->id === null) return false;

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();

        if ($row) {
            return password_verify($password, $row['password']);
        }

        return false;
    }

    public function updatePassword(string $newPassword): bool
    {
        if ($this->id === null) return false;

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $this->id]);
    }
}
