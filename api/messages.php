<?php
declare(strict_types=1);
/**
 * JSON API — customer messages.
 * GET  api/messages.php   -> thread
 * POST api/messages.php   { subject, message } -> send
 */
require_once __DIR__ . '/_helpers.php';

$customer = api_require_customer();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->prepare('
        SELECT id, sender_type, subject, message, is_read, created_at
        FROM messages
        WHERE (sender_type = "customer" AND sender_id = ?)
           OR (sender_type = "admin" AND receiver_type = "customer" AND receiver_id = ?)
        ORDER BY created_at DESC LIMIT 50
    ');
    $stmt->execute([$customer['id'], $customer['id']]);
    api_json(200, ['messages' => array_reverse($stmt->fetchAll())]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $subject = trim((string)($input['subject'] ?? ''));
    $message = trim((string)($input['message'] ?? ''));
    if ($subject === '' || strlen($message) < 2) {
        api_json(422, ['error' => 'Subject and message are required.']);
    }
    db()->prepare('INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, subject, message, is_read) VALUES ("customer", ?, "admin", NULL, ?, ?, 0)')
        ->execute([$customer['id'], $subject, $message]);
    notify(1, 'admin', 'New message from customer', $customer['name'] . ': ' . $subject, 'admin/messages.php');
    api_json(201, ['ok' => true, 'message' => 'Message sent.']);
}

api_json(405, ['error' => 'Method not allowed.']);
