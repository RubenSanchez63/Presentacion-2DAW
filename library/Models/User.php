<?php

namespace Models;

use Clases\Database;

class User
{
    private int    $id;
    public private(set) string $email;
    public private(set) string $name;
    public private(set) string $role;      // 'user' | 'librarian' | 'admin'
    private string $passwordHash;
    private string $createdAt;

    // ── Auth ──────────────────────────────────────────────

    private function verify(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public static function getByEmailAndPassword(string $email, string $password): User|false
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetchObject(self::class);

        if ($user instanceof self && $user->verify($password)) {
            return $user;
        }
        return false;
    }

    public static function getByEmail(string $email): User|false
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchObject(self::class);
    }

    public static function getById(int $id): User|false
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject(self::class);
    }

    // ── CRUD ─────────────────────────────────────────────

    public static function create(string $email, string $password, string $name, string $role = 'user'): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO User (email, passwordHash, name, role)
             VALUES (:email, :passwordHash, :name, :role)"
        );
        return $stmt->execute([
            ':email'        => $email,
            ':passwordHash' => password_hash($password, PASSWORD_DEFAULT),
            ':name'         => $name,
            ':role'         => $role,
        ]);
    }

    /** Only admins should call this */
    public static function listAll(): array
    {
        $pdo  = Database::connect();
        $stmt = $pdo->query("SELECT id, email, name, role, createdAt FROM User");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateRole(int $id, string $newRole): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("UPDATE User SET role = :role WHERE id = :id");
        return $stmt->execute([':role' => $newRole, ':id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM User WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Actualiza nombre, email, rol y opcionalmente la contraseña.
     * Si $password es null no se modifica el hash actual.
     */
    public static function updateUser(int $id, string $name, string $email, string $role, ?string $password = null): bool
    {
        $pdo = Database::connect();

        if ($password !== null) {
            $stmt = $pdo->prepare(
                "UPDATE User
             SET name = :name, email = :email, role = :role, passwordHash = :passwordHash
             WHERE id = :id"
            );
            return $stmt->execute([
                ':name'         => $name,
                ':email'        => $email,
                ':role'         => $role,
                ':passwordHash' => password_hash($password, PASSWORD_DEFAULT),
                ':id'           => $id,
            ]);
        }

        $stmt = $pdo->prepare(
            "UPDATE User
         SET name = :name, email = :email, role = :role
         WHERE id = :id"
        );
        return $stmt->execute([
            ':name'  => $name,
            ':email' => $email,
            ':role'  => $role,
            ':id'    => $id,
        ]);
    }

    // ── Read-only public getters ──────────────────────────

    public function getId(): int
    {
        return $this->id;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    /** Serialize without exposing passwordHash */
    public function toPublicArray(): array
    {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'name'  => $this->name,
            'role'  => $this->role,
        ];
    }
}
