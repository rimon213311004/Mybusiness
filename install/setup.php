#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * RimonTech one-time setup:
 *  1. Create MySQL database + user
 *  2. Import schema.sql and seed.sql
 *  3. Build sample downloadable ZIP projects
 *
 * Run:  php install/setup.php
 */

echo "=== RimonTech Setup ===\n";

$root = dirname(__DIR__);
$schema = $root . '/database/schema.sql';
$seed   = $root . '/database/seed.sql';

// 1. Create database and user
$sql = <<<SQL
CREATE DATABASE IF NOT EXISTS rimontech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'rimontech'@'localhost' IDENTIFIED BY 'rimontech_dev_2026';
CREATE USER IF NOT EXISTS 'rimontech'@'127.0.0.1' IDENTIFIED BY 'rimontech_dev_2026';
GRANT ALL PRIVILEGES ON rimontech.* TO 'rimontech'@'localhost';
GRANT ALL PRIVILEGES ON rimontech.* TO 'rimontech'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL;

$tmp = tempnam(sys_get_temp_dir(), 'rt_');
file_put_contents($tmp, $sql);
echo "[1/3] Creating database + user ... ";
passthru('mysql -uroot < ' . escapeshellarg($tmp) . ' 2>&1', $code);
if ($code !== 0) {
    fwrite(STDERR, "FAILED\n");
    exit(1);
}
unlink($tmp);
echo "OK\n";

// 2. Import schema + seed
echo "[2/3] Importing schema ... ";
passthru('mysql -uroot < ' . escapeshellarg($schema) . ' 2>&1', $code);
if ($code !== 0) { fwrite(STDERR, "FAILED\n"); exit(1); }
echo "OK\n";

echo "      Importing seed data ... ";
passthru('mysql -uroot < ' . escapeshellarg($seed) . ' 2>&1', $code);
if ($code !== 0) { fwrite(STDERR, "FAILED\n"); exit(1); }
echo "OK\n";

// 3. Build sample ZIP downloads
echo "[3/3] Building sample downloadable projects ...\n";
buildZips($root);

echo "\n=== Setup complete ===\n";
echo "Public website : http://localhost:8080/\n";
echo "Admin login    : admin@rimontech.com / Admin@123\n";
echo "Customer login : customer@rimontech.com / Customer@123\n";

function buildZips(string $root): void
{
    $outDir = $root . '/downloads/projects';
    if (!is_dir($outDir)) {
        mkdir($outDir, 0777, true);
    }

    $projects = [
        'job-tracking-app'    => 'Job Tracking Application',
        'ats-cv-maker'        => 'ATS CV Maker',
        'rice-shop-inventory' => 'Rice Shop Inventory',
        'karim-traders-website' => 'Karim Traders Website',
    ];

    foreach ($projects as $slug => $title) {
        $zipPath = $outDir . '/' . $slug . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            echo "      ! could not create $zipPath\n";
            continue;
        }

        $prefix = 'project-source/';
        $readme = "# $title\n\nA sample public source bundle provided by RimonTech for learning purposes.\n\n"
            . "## Structure\n\n"
            . "- `frontend/` - UI source files\n"
            . "- `backend/` - API / server source files\n"
            . "- `assets/`  - static assets\n"
            . "- `database/database.sql` - example schema only (no real credentials)\n"
            . "- `README.md`\n"
            . "- `LICENSE.txt`\n\n"
            . "## Getting Started\n\n"
            . "Follow the instructions in each subfolder to run the project locally.\n\n"
            . "> This is a sanitized demo bundle. Production credentials, API keys and private data "
            . "are intentionally excluded.\n";

        $license = "MIT License\n\nCopyright (c) " . date('Y') . " RimonTech\n\n"
            . "Permission is hereby granted, free of charge, to any person obtaining a copy of this "
            . "software and associated documentation files (the \"Software\"), to deal in the Software "
            . "without restriction, including without limitation the rights to use, copy, modify, merge, "
            . "publish, distribute, sublicense, and/or sell copies of the Software.\n";

        $dbSql = "-- Example database schema for $slug\n"
            . "-- NOTE: sanitized demo only - no real credentials included.\n\n"
            . "CREATE TABLE IF NOT EXISTS sample_items (\n"
            . "    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n"
            . "    name VARCHAR(120) NOT NULL,\n"
            . "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n"
            . ");\n";

        $files = [
            $prefix . 'README.md'              => $readme,
            $prefix . 'LICENSE.txt'            => $license,
            $prefix . 'database/database.sql'  => $dbSql,
            $prefix . 'frontend/index.html'    => "<!DOCTYPE html>\n<html>\n<head>\n<title>$title - Demo</title>\n</head>\n<body>\n<h1>$title</h1>\n<p>Sample frontend file. Real code omitted for this public bundle.</p>\n</body>\n</html>\n",
            $prefix . 'frontend/app.js'        => "// Sample frontend entry point\n// Real implementation is excluded from the public bundle.\nconsole.log('$slug demo');\n",
            $prefix . 'backend/api.js'         => "// Sample backend entry point\n// Real implementation is excluded from the public bundle.\nmodule.exports = { name: '$slug' };\n",
            $prefix . 'assets/.gitkeep'        => '',
        ];

        foreach ($files as $path => $content) {
            $zip->addFromString($path, $content);
        }
        $zip->close();
        echo "      built $slug.zip\n";
    }
}
