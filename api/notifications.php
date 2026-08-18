<?php
declare(strict_types=1);
/**
 * JSON API — customer notifications.
 * GET  api/notifications.php                 -> list
 * POST api/notifications.php { action: "read_all" } -> mark all read
 */
require_once __DIR__ . '/_helpers.php';

$customer = api_require_customer();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->prepare('SELECT id, title, message, link, is_read, created_at FROM notifications WHERE user_type = "customer" AND user_id = ? ORDER BY created_at DESC LIMIT 30');
    $stmt->execute([$customer['id']]);
    api_json(200, ['notifications' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    if (($input['action'] ?? '') === 'read_all') {
        db()->prepare('UPDATE notifications SET is_read = 1 WHERE user_type = "customer" AND user_id = ?')->execute([$customer['id']]);
        api_json(200, ['ok' => true]);
    }
    api_json(400, ['error' => 'Unknown action.']);
}

api_json(405, ['error' => 'Method not allowed.']);
