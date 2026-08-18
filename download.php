<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$fileName = basename((string)($_GET['file'] ?? ''));

if ($fileName === '' || $fileName === '.' || $fileName === '..') {
    http_response_code(404);
    exit('Invalid download request.');
}

// Resolve the real file and ensure it stays inside the downloads/projects directory
$projectDir = realpath(DOWNLOAD_DIR . '/projects');
if ($projectDir === false) {
    http_response_code(500);
    exit('Download directory not available.');
}
$target = realpath($projectDir . '/' . $fileName);
if ($target === false || !is_file($target) || !str_starts_with($target, $projectDir)) {
    http_response_code(404);
    exit('File not found.');
}

// Only allow downloads registered in the database and currently enabled
$stmt = db()->prepare('SELECT * FROM downloads WHERE file_name = ?');
$stmt->execute([$fileName]);
$download = $stmt->fetch();
if (!$download || (int)$download['download_enabled'] !== 1) {
    http_response_code(403);
    exit('This download is disabled.');
}

// Increment the download counter
db()->prepare('UPDATE downloads SET download_count = download_count + 1 WHERE id = ?')
    ->execute([$download['id']]);

// Stream the file to the browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . rawurlencode($download['file_name']) . '"');
header('Content-Length: ' . filesize($target));
header('X-Content-Type-Options: nosniff');
readfile($target);
exit;
