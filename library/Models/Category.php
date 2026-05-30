<?php

namespace Models;

use Clases\Database;
use PDO;

class Category
{
    public private(set) int    $id;
    public private(set) string $name;

    public static function listAll(): array
    {
        $pdo  = Database::connect();
        $stmt = $pdo->query("SELECT * FROM Category ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getById(int $id): Category|false
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM Category WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject(self::class);
    }

    public static function create(string $name): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO Category (name) VALUES (:name)"
        );
        return $stmt->execute([':name' => $name]);
    }

    public static function update(int $id, string $name): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "UPDATE Category SET name = :name WHERE id = :id"
        );
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM Category WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
