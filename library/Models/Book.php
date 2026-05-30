<?php

namespace Models;

use Clases\Database;
use PDO;


class Book
{
    public private(set) int     $id;
    public private(set) string  $title;
    public private(set) string  $author;
    public private(set) string  $description;
    public private(set) string  $cover;           // cover image path
    public private(set) string  $pdfPath;         // PDF file path
    public private(set) int     $pages;
    public private(set) int     $category_id;
    public private(set) int     $uploaded_by;     // user id
    public private(set) string  $uploadedAt;

    // For joined queries
    public ?string $categoryName = null;
    public ?string $uploadedByName = null;

    // ── Listings ─────────────────────────────────────────

    /** Returns all books */
    public static function listAll(?string $status = null): array
    {
        $pdo  = Database::connect();
        $stmt = $pdo->query(
            "SELECT b.*, c.name AS categoryName, u.name AS uploadedByName
             FROM Book b
             JOIN Category c ON b.category_id = c.id
             JOIN User u ON b.uploaded_by = u.id
             ORDER BY b.uploadedAt DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /** Books uploaded by a specific user */
    public static function listByUser(int $userId): array
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT b.*, c.name AS categoryName
             FROM Book b
             JOIN Category c ON b.category_id = c.id
             WHERE b.uploaded_by = :uid
             ORDER BY b.uploadedAt DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getById(int $id): Book|false
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT b.*, c.name AS categoryName
             FROM Book b
             JOIN Category c ON b.category_id = c.id
             WHERE b.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject(self::class);
    }

    // ── Write ─────────────────────────────────────────────

    public static function create(array $data): bool
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO Book (title, author, description, cover, pdfPath, pages, category_id, uploaded_by)
             VALUES (:title, :author, :description, :cover, :pdfPath, :pages, :category_id, :uploaded_by)"
        );
        return $stmt->execute($data);
    }

    public static function update(int $id, array $data): string
    {
        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare(
                "UPDATE Book
                 SET title = :title, author = :author, description = :description,
                     cover = :cover, pages = :pages, category_id = :category_id
                 WHERE id = :id"
            );
            $data[':id'] = $id;
            $result = $stmt->execute($data);
            return $result ? "Libro actualizado correctamente" : "No se ha podido actualizar el libro.";
        } catch (\PDOException $e) {
            return "Error de base de datos: " . $e->getMessage();
        }
    }

    public static function delete(int $id): Book|false
    {
        $book = self::getById($id);
        if (!$book) return false;

        $pdo  = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM Book WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $book; // returned so physical files can be deleted
    }

    // ── Search ────────────────────────────────────────────

    public static function search(string $q): array
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT b.*, c.name AS categoryName
             FROM Book b
             JOIN Category c ON b.category_id = c.id
             WHERE b.title LIKE :q OR b.author LIKE :q OR b.description LIKE :q
             ORDER BY b.title"
        );
        $stmt->execute([':q' => "%$q%"]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
