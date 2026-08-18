<?php
declare(strict_types=1);
/**
 * JSON API — public downloads list.
 * GET api/downloads.php -> list of public (enabled) downloadable projects
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$rows = db()->query('
    SELECT d.id, d.file_name, d.download_count, d.download_enabled,
           p.id AS project_id, p.title AS project_title, p.tech_stack, p.category
    FROM downloads d
    JOIN portfolio_items p ON p.id = d.project_id
    WHERE d.download_enabled = 1
    ORDER BY d.download_count DESC
')->fetchAll();

$items = array_map(function ($r) {
    return [
        'download_id' => (int)$r['id'],
        'project_id' => (int)$r['project_id'],
        'project_title' => $r['project_title'],
        'category' => $r['category'],
        'tech_stack' => $r['tech_stack'],
        'file_name' => $r['file_name'],
        'download_count' => (int)$r['download_count'],
        'url' => base_url('download.php?file=' . rawurlencode($r['file_name'])),
    ];
}, $rows);

echo json_encode(['downloads' => $items], JSON_UNESCAPED_UNICODE);
