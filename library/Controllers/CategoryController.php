<?php

namespace Controllers;

use Models\Category;
use Clases\Auth;

class CategoryController
{
    // ── GET /categories ───────────────────────────────────
    // Public: anyone can view categories

    public function index(): void
    {
        echo json_encode(Category::listAll());
    }

    // ── GET /categories/{id} ─────────────────────────────

    public function getById(int $id): void
    {
        $category = Category::getById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Category not found.']);
        }
    }

    // ── POST /categories  (admin only) ────────────────────

    public function create(): void
    {
        Auth::requireAuth('admin');

        $data        = json_decode(file_get_contents('php://input'), true);
        $name        = trim($data['name']        ?? '');
        $description = trim($data['description'] ?? '');

        if (!$name) {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required.']);
            return;
        }

        if (Category::create($name)) {
            http_response_code(201);
            echo json_encode(['message' => 'Category created successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create category.']);
        }
    }

    // ── PUT /categories/{id}  (admin only) ────────────────

    public function update(int $id): void
    {
        Auth::requireAuth('admin');

        $data        = json_decode(file_get_contents('php://input'), true);
        $name        = trim($data['name']        ?? '');

        if (!$name) {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required.']);
            return;
        }

        echo json_encode(['result' => Category::update($id, $name)]);
    }

    // ── DELETE /categories/{id}  (admin only) ─────────────

    public function delete(int $id): void
    {
        Auth::requireAuth('admin');
        echo json_encode(['result' => Category::delete($id)]);
    }
}
