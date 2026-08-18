<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

function api_require_customer(): array
{
    $customer = current_customer();
    if (!$customer) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Not authenticated.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    return $customer;
}

function api_json(int $code, array $data): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function api_input(): array
{
    $input = json_decode(file_get_contents('php://input'), true);
    return is_array($input) ? $input : $_POST;
}
