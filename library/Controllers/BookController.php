<?php

namespace Controllers;

use Models\Book;
use Clases\Auth;
use Clases\Sesion;

class BookController
{
    private const PDF_DIR     = __DIR__ . '/../assets/pdfs/';
    private const COVER_DIR   = __DIR__ . '/../assets/covers/';
    private const MAX_PDF_MB  = 50;
    private const MAX_IMG_MB  = 5;

    // ── GET /books ────────────────────────────────────────

    public function index(): void
    {
        $books = Book::listAll();

        // Remove JSON content-type for HTML views
        header('Content-Type: text/html; charset=utf-8');

        // Pass categories for filter dropdown and add-book modal
        $categories = \Models\Category::listAll();

        // Build stats
        $stats = [
            'Total Books'      => count($books),
            'Registered Users' => count(\Models\User::listAll()),
            'Categories'       => count($categories),
        ];

        include __DIR__ . '/../Views/dashboard.php';
    }

    // ── GET /books/{id} ───────────────────────────────────

    public function getById(int $id): void
    {
        $book = Book::getById($id);
        if (!$book) {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found.']);
            return;
        }

        echo json_encode($book);
    }

    // ── GET /books/my-books ───────────────────────────────
    // Authenticated user: view their own uploaded books

    public function myBooks(): void
    {
        $payload = Auth::requireAuth(Sesion::getRole());
        echo json_encode(Book::listByUser($payload['sub']));
    }

    // ── GET /books/search?q=... ───────────────────────────

    public function search(): void
    {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            http_response_code(400);
            echo json_encode(['error' => 'Search query must be at least 2 characters.']);
            return;
        }
        echo json_encode(Book::search($q));
    }

    // ── POST /books  (authenticated user or admin) ────────

    public function upload(): void
    {
        $payload = Auth::requireAuth(Sesion::getRole()); // any logged-in user

        // ── Validate text fields ──
        $title       = trim($_POST['title']       ?? '');
        $author      = trim($_POST['author']      ?? '');
        $description = trim($_POST['description'] ?? '');
        $pages       = (int)($_POST['pages']      ?? 0);
        $categoryId  = (int)($_POST['category_id'] ?? 0);

        if (!$title || !$author || !$categoryId) {
            http_response_code(400);
            echo json_encode(['error' => 'Title, author and category are required.']);
            return;
        }

        // ── Validate and save PDF ──
        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'A PDF file is required.']);
            return;
        }

        $pdfFile = $_FILES['pdf'];
        if ($pdfFile['size'] > self::MAX_PDF_MB * 1024 * 1024) {
            http_response_code(413);
            echo json_encode(['error' => 'PDF exceeds the ' . self::MAX_PDF_MB . ' MB limit.']);
            return;
        }

        $mime = mime_content_type($pdfFile['tmp_name']);
        if ($mime !== 'application/pdf') {
            http_response_code(415);
            echo json_encode(['error' => 'Only PDF files are accepted.']);
            return;
        }

        $pdfFilename = uniqid('pdf_', true) . '.pdf';
        if (!move_uploaded_file($pdfFile['tmp_name'], self::PDF_DIR . $pdfFilename)) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not save the PDF file.']);
            return;
        }

        // ── Validate and save cover image (optional) ──
        $coverFilename = 'default.jpg';
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverFile   = $_FILES['cover'];
            $allowedImg  = ['image/jpeg', 'image/png', 'image/webp'];
            $mimeImg     = mime_content_type($coverFile['tmp_name']);

            if ($coverFile['size'] <= self::MAX_IMG_MB * 1024 * 1024 && in_array($mimeImg, $allowedImg)) {
                $ext           = match ($mimeImg) {
                    'image/png'  => '.png',
                    'image/webp' => '.webp',
                    default      => '.jpg',
                };
                $coverFilename = uniqid('cover_', true) . $ext;
                if (!move_uploaded_file($coverFile['tmp_name'], self::COVER_DIR . $coverFilename)) {
                    $coverFilename = 'default.jpg';
                }
            }
        }

        // ── Insert into database ──
        $data = [
            ':title'       => $title,
            ':author'      => $author,
            ':description' => $description,
            ':cover'       => $coverFilename,
            ':pdfPath'     => $pdfFilename,
            ':pages'       => $pages,
            ':category_id' => $categoryId,
            ':uploaded_by' => $payload['sub'],
        ];

        if (Book::create($data)) {
            http_response_code(201);
            echo json_encode(['message' => 'Book uploaded successfully.']);
        } else {
            // Clean up orphaned files on DB failure
            @unlink(self::PDF_DIR   . $pdfFilename);
            @unlink(self::COVER_DIR . $coverFilename);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register the book.']);
        }
    }

    // ── PUT /books/{id} ─────

    public function update(int $id): void
    {
        $payload = Auth::requireAuth(Sesion::getRole());

        $book = Book::getById($id);
        if (!$book) {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found.']);
            return;
        }

        if ($payload['role'] !== 'admin' && $payload['role'] !== 'librarian') {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to edit this book.']);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // ── Parsear el body según Content-Type ──
        if (str_contains($contentType, 'multipart/form-data')) {
            // Los campos de texto vienen en $_POST cuando hay archivos
            $data = $_POST;
        } elseif (str_contains($contentType, 'application/json')) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            // application/x-www-form-urlencoded
            parse_str(file_get_contents('php://input'), $data);
        }

        $coverFilename = $book->cover;

        // ── Handle cover image upload (opcional) ──
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverFile  = $_FILES['cover'];
            $allowedImg = ['image/jpeg', 'image/png', 'image/webp'];
            $mimeImg    = mime_content_type($coverFile['tmp_name']);

            if ($coverFile['size'] <= self::MAX_IMG_MB * 1024 * 1024 && in_array($mimeImg, $allowedImg)) {
                $ext = match ($mimeImg) {
                    'image/png'  => '.png',
                    'image/webp' => '.webp',
                    default      => '.jpg',
                };
                $newCoverFilename = uniqid('cover_', true) . $ext;
                if (move_uploaded_file($coverFile['tmp_name'], self::COVER_DIR . $newCoverFilename)) {
                    if ($book->cover !== 'default.jpg') {
                        @unlink(self::COVER_DIR . $book->cover);
                    }
                    $coverFilename = $newCoverFilename;
                }
            } else {
                http_response_code(415);
                echo json_encode(['error' => 'Cover must be JPEG, PNG or WebP and not exceed ' . self::MAX_IMG_MB . ' MB.']);
                return;
            }
        }

        $fields = [
            ':title'       => trim($data['title']       ?? $book->title),
            ':author'      => trim($data['author']      ?? $book->author),
            ':description' => trim($data['description'] ?? $book->description),
            ':cover'       => $coverFilename,
            ':pages'       => (int)($data['pages']      ?? $book->pages),
            ':category_id' => (int)($data['category_id'] ?? $book->category_id),
        ];

        echo json_encode(['result' => Book::update($id, $fields)]);
    }

    // ── DELETE /books/{id}  (admin only) ──────────────────

    public function delete(int $id): void
    {
        Auth::requireAuth('admin');

        $book = Book::delete($id);
        if (!$book) {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found.']);
            return;
        }

        // Delete physical files
        @unlink(self::PDF_DIR   . $book->pdfPath);
        if ($book->cover !== 'default.jpg') {
            @unlink(self::COVER_DIR . $book->cover);
        }

        echo json_encode(['message' => 'Book deleted successfully.']);
    }
}
