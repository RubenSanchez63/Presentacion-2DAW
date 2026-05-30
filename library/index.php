<?php

require_once 'autoload.php';

use Controllers\BookController;
use Controllers\CategoryController;
use Controllers\UserController;
use Clases\Sesion;
use Clases\Redirect;

// ── REST Headers ─────────────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── Parse URI ────────────────────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = trim($uri, '/');
// Ignorar la carpeta base si está presente
if (strpos($uri, 'library') === 0) {
    $uri = substr($uri, strlen('library'));
}
$uri    = trim($uri, '/'); // Trim nuevamente después de remover la carpeta base
$parts    = array_values(array_filter(explode('/', $uri), 'strlen')); // ← array_values()
$method = $_SERVER['REQUEST_METHOD'];

// Method override para multipart/form-data
if ($method === 'POST' && isset($_GET['_method'])) {
    $method = strtoupper($_GET['_method']);
}

$resource = $parts[0] ?? '';
$id       = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
$action   = isset($parts[1]) && !is_numeric($parts[1]) ? $parts[1] : ($parts[2] ?? null);
// ── Verificar autenticación JWT ───────────────────────────────────────────
// Inicializar sesión desde JWT si existe token válido
if (!Sesion::active()) {
    $payload = Sesion::getTokenPayload();
    if ($payload) {
        Sesion::initFromToken($_COOKIE['auth_token'] ?? '');
    }
}
// ── Router ────────────────────────────────────────────────────────────────────
switch ($resource) {

    case 'books':
        $ctrl = new BookController();
        if ($method === 'GET') {
            if ($id) {
                $ctrl->getById($id);
            } elseif ($action === 'search') {
                $ctrl->search();
            } elseif ($action === 'my-books') {
                $ctrl->myBooks();
            } else {
                $ctrl->index();
            }
        } elseif ($method === 'POST') {
            $ctrl->upload();
        } elseif ($method === 'PUT'   && $id) {
            $ctrl->update($id);
        } elseif ($method === 'DELETE' && $id) {
            $ctrl->delete($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request.']);
        }
        break;

    case 'categories':
        $ctrl = new CategoryController();
        if ($method === 'GET') {
            $id ? $ctrl->getById($id) : $ctrl->index();
        } elseif ($method === 'POST') {
            $ctrl->create();
        } elseif ($method === 'PUT'  && $id) {
            $ctrl->update($id);
/*         } elseif ($method === 'DELETE' && $id) {
            $ctrl->delete($id); */
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request.']);
        }
        break;

    case 'login':
        if ($method === 'GET') {
            (new UserController())->renderLogin();
        } elseif ($method === 'POST') {
            (new UserController())->login();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;

    case 'register':
        if ($method === 'GET') {
            (new UserController())->renderRegister();
        } elseif ($method === 'POST') {
            (new UserController())->register();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;

    case 'users':
        $ctrl = new UserController();
        if ($method === 'GET') {
            if ($action === 'manage') {
                $ctrl->manageUsers();
            } elseif ($action === 'create') {
                $ctrl->renderCreateUser();
            } elseif ($action === 'me') {
                $ctrl->profile();
            } elseif ($action === 'edit' && isset($parts[2]) && is_numeric($parts[2])) {
                $ctrl->renderEditUser((int)$parts[2]);
            }
        } elseif ($method === 'POST') {
            if ($action === 'store') {
                $ctrl->storeUser();
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request.']);
            }
        } elseif ($method === 'PUT' && $id) {
            $ctrl->update($id);       // PUT /users/{id}       (edición completa)
        } elseif ($method === 'DELETE'   && $id) {
            $ctrl->delete($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request.']);
        }
        break;

    case 'logout':
        if ($method === 'GET') {
            (new UserController())->logout();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;

    default:
        if (empty($resource)) {
            if (Sesion::isAuthenticated()) {
                Redirect::to('books');
            } else {
                Redirect::to('login');
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found.']);
            /*             var_dump($resource); */
        }
        break;
}
