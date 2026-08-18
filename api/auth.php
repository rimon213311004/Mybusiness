<?php
declare(strict_types=1);
/**
 * RimonTech JSON API — authentication.
 * POST api/auth.php  { action: "login", email, password }  -> { token, user }
 * POST api/auth.php  { action: "logout" }
 * POST api/auth.php  { action: "me" }
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

function json_response(int $code, array $data): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method !== 'POST') {
    json_response(405, ['error' => 'Method not allowed.']);
}

$action = $input['action'] ?? '';

switch ($action) {
    case 'login':
        $email = trim((string)($input['email'] ?? ''));
        $password = (string)($input['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            json_response(422, ['error' => 'Email and password are required.']);
        }
        $stmt = db()->prepare('SELECT * FROM customers WHERE email = ?');
        $stmt->execute([$email]);
        $customer = $stmt->fetch();
        if (!$customer || !password_verify($password, $customer['password'])) {
            json_response(401, ['error' => 'Invalid credentials.']);
        }
        if ($customer['status'] !== 'active') {
            json_response(403, ['error' => 'Account is inactive.']);
        }
        session_regenerate_id(true);
        $_SESSION['customer_id'] = (int)$customer['id'];
        json_response(200, ['ok' => true, 'token' => session_id(), 'user' => ['id' => (int)$customer['id'], 'name' => $customer['name'], 'email' => $customer['email']]]);

    case 'logout':
        session_unset();
        session_destroy();
        json_response(200, ['ok' => true]);

    case 'me':
        $customer = current_customer();
        if (!$customer) {
            json_response(401, ['error' => 'Not authenticated.']);
        }
        json_response(200, ['user' => ['id' => (int)$customer['id'], 'name' => $customer['name'], 'email' => $customer['email'], 'phone' => $customer['phone'], 'company' => $customer['company']]]);

    default:
        json_response(400, ['error' => 'Unknown action.']);
}
