<?php

namespace Controllers;

use Models\User;
use Clases\JWT;
use Clases\Auth;
use Clases\Redirect;
use Clases\Sesion;

class UserController
{
    // ── POST /login ───────────────────────────────────────

    public function renderLogin(): void
    {
        require_once __DIR__ . '/../Views/formLogin.php';
    }

    public function login(): void
    {
        // Detectar si es petición JSON o formulario tradicional
        $isJson = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
        $data   = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;
        $email  = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required.']);
            return;
        }

        $user = User::getByEmailAndPassword($email, $password);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials.']);
            return;
        }

        // Generar JWT
        $token = JWT::generate([
            'sub'   => $user->getId(),
            'email' => $user->getEmail(),
            'name'  => $user->getName(),
            'role'  => $user->getRole(),
            'exp'   => time() + 60 * 60 * 8, // 8 hours
        ]);

        // Guardar token en cookie HttpOnly
        setcookie('auth_token', $token, [
            'expires'  => time() + 60 * 60 * 8,
            'path'     => '/',
            'httponly' => true,
            'secure'   => false, // Cambiar a true en producción con HTTPS
            'samesite' => 'Lax'
        ]);

        // Si es petición JSON, retornar token
        if ($isJson) {
            echo json_encode([
                'success' => true,
                'token'   => $token,
                'user'    => $user->toPublicArray()
            ]);
        } else {
            // Si es formulario, inicializar sesión y redirigir
            Sesion::initFromToken($token);
            Redirect::to('/');
        }
    }

    // ── POST /register ────────────────────────────────────

    public function renderRegister(): void
    {
        require_once __DIR__ . '/../Views/formRegister.php';
    }

    public function register(): void
    {
        $data     = $_POST;
        $email    = trim($data['email']    ?? '');
        $password = trim($data['password'] ?? '');
        $name     = trim($data['name']     ?? '');

        if (!$email || !$password || !$name) {
            http_response_code(400);
            echo json_encode(['error' => 'Email, password and name are required.']);
            return;
        }

        if (User::getByEmail($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already registered.']);
            return;
        }

        if (User::create($email, $password, $name)) {
            Redirect::to('/');
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register user.']);
        }
    }

    // ── GET /users  (admin only) ──────────────────────────

    public function list(): void
    {
        Auth::requireAuth('admin');
        echo json_encode(User::listAll());
    }

    // ── GET /users/edit/{id}  (admin only) ───────────────────────
    public function renderEditUser(int $id): void
    {
        Auth::requireAuth('admin');

        $user = User::getById($id);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found.']);
            return;
        }

        // Convertir el objeto a array para la vista
        $user = $user->toPublicArray();
        require_once __DIR__ . '/../Views/editUser.php';
    }

    // ── PUT /users/{id}/role  (admin only) ────────────────
    public function update(int $id): void
    {
        Auth::requireAuth('admin');

        $data     = json_decode(file_get_contents('php://input'), true);
        $name     = trim($data['name']     ?? '');
        $email    = trim($data['email']    ?? '');
        $role     = trim($data['role']     ?? '');
        $password = trim($data['password'] ?? '');

        // Validaciones básicas
        if (!$name || !$email || !$role) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email and role are required.']);
            return;
        }

        if (!in_array($role, ['user', 'librarian', 'admin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role. Use "user", "librarian" or "admin".']);
            return;
        }

        if ($password !== '' && strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['error' => 'The password must have at least 8 characters.']);
            return;
        }

        // Verificar que el email no esté en uso por otro usuario
        $existing = User::getByEmail($email);
        if ($existing && $existing->getId() !== $id) {
            http_response_code(409);
            echo json_encode(['error' => 'This email is already in use by another account.']);
            return;
        }

        $result = User::updateUser($id, $name, $email, $role, $password !== '' ? $password : null);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error updating the user.']);
        }
    }

    // ── DELETE /users/{id}  (admin only) ─────────────────

    public function delete(int $id): void
    {
        $payload = Auth::requireAuth('admin');

        if ($payload['sub'] === $id) {
            http_response_code(400);
            echo json_encode(['error' => 'You cannot delete your own account.']);
            return;
        }

        echo json_encode(['result' => User::delete($id)]);
    }

    // ── GET /logout ──────────────────────────────────────

    public function logout(): void
    {
        // Limpiar cookie
        setcookie('auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'secure'   => false,
            'samesite' => 'Lax'
        ]);

        // Destruir sesión
        Sesion::close();

        // Redirigir a login
        Redirect::to('/login');
    }

    // ── GET /users/manage  (admin only) ──────────────────

    public function manageUsers(): void
    {
        Auth::requireAuth('admin');

        // Obtener todos los usuarios
        $users = User::listAll();

        require_once __DIR__ . '/../Views/manageUsers.php';
    }

    // ── GET /users/create  (admin only) ──────────────────

    public function renderCreateUser(): void
    {
        Auth::requireAuth('admin');
        require_once __DIR__ . '/../Views/createUser.php';
    }

    // ── POST /users/store  (admin only) ──────────────────

    public function storeUser(): void
    {
        Auth::requireAuth('admin');

        $data     = json_decode(file_get_contents('php://input'), true);
        $email    = trim($data['email']    ?? '');
        $password = trim($data['password'] ?? '');
        $name     = trim($data['name']     ?? '');
        $role     = trim($data['role']     ?? 'user');

        // Validaciones
        if (!$email || !$password || !$name) {
            http_response_code(400);
            echo json_encode(['error' => 'Email, password and name are required.']);
            return;
        }

        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['error' => 'The password must have at least 8 characters.']);
            return;
        }

        if (!in_array($role, ['user', 'librarian', 'admin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role. Use "user", "librarian" or "admin".']);
            return;
        }

        if (User::getByEmail($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'This email is already registered.']);
            return;
        }

        if (User::create($email, $password, $name, $role)) {
            echo json_encode(['success' => true, 'message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error creating the user.']);
        }
    }

    // ── GET /users/me  (any authenticated user) ───────────

    public function profile(): void
    {
        $payload = Auth::requireAuth(Sesion::getRole());
        $user    = User::getById($payload['sub']);

        if ($user) {
            echo json_encode($user->toPublicArray());
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found.']);
        }
    }
}
