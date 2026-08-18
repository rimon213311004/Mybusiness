<?php
declare(strict_types=1);
/**
 * JSON API — customer projects.
 * GET  api/projects.php            -> list
 * GET  api/projects.php?id=N       -> single project with invoices/documents
 */
require_once __DIR__ . '/_helpers.php';

$customer = api_require_customer();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_json(405, ['error' => 'Method not allowed.']);
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = db()->prepare('SELECT id, title, description, status, progress, start_date, due_date, created_at FROM projects WHERE id = ? AND customer_id = ?');
    $stmt->execute([$id, $customer['id']]);
    $project = $stmt->fetch();
    if (!$project) {
        api_json(404, ['error' => 'Project not found.']);
    }
    $inv = db()->prepare('SELECT invoice_no, amount, status, due_date FROM invoices WHERE customer_id = ? AND project_id = ?');
    $inv->execute([$customer['id'], $project['id']]);
    $project['invoices'] = $inv->fetchAll();
    api_json(200, ['project' => $project]);
}

$stmt = db()->prepare('SELECT id, title, status, progress, due_date, created_at FROM projects WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
api_json(200, ['projects' => $stmt->fetchAll()]);
