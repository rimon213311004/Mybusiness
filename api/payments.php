<?php
declare(strict_types=1);
/**
 * JSON API — customer payments.
 * GET  api/payments.php                       -> history + unpaid invoices
 * POST api/payments.php { invoice_id, amount, method, trx_id } -> submit payment
 */
require_once __DIR__ . '/_helpers.php';

$customer = api_require_customer();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->prepare('SELECT id, amount, method, trx_id, status, created_at FROM payments WHERE customer_id = ? ORDER BY created_at DESC');
    $stmt->execute([$customer['id']]);
    $unpaid = db()->prepare('SELECT id, invoice_no, amount, status FROM invoices WHERE customer_id = ? AND status IN ("unpaid","overdue")');
    $unpaid->execute([$customer['id']]);
    api_json(200, ['payments' => $stmt->fetchAll(), 'unpaid_invoices' => $unpaid->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = api_input();
    $invoiceId = (int)($input['invoice_id'] ?? 0);
    $amount = (float)($input['amount'] ?? 0);
    $method = trim((string)($input['method'] ?? ''));
    $trxId = trim((string)($input['trx_id'] ?? ''));

    $inv = db()->prepare('SELECT * FROM invoices WHERE id = ? AND customer_id = ? AND status IN ("unpaid","overdue")');
    $inv->execute([$invoiceId, $customer['id']]);
    $invoice = $inv->fetch();
    if (!$invoice) api_json(422, ['error' => 'Invalid invoice.']);
    if ($amount <= 0 || $amount > (float)$invoice['amount']) api_json(422, ['error' => 'Invalid amount.']);
    if ($method === '') api_json(422, ['error' => 'Payment method is required.']);

    db()->prepare('INSERT INTO payments (customer_id, invoice_id, amount, method, trx_id, status) VALUES (?, ?, ?, ?, ?, "pending")')
        ->execute([$customer['id'], $invoiceId, $amount, $method, $trxId ?: null]);
    notify(1, 'admin', 'New payment submitted', $customer['name'] . ' submitted a ' . $method . ' payment of ' . money($amount), 'admin/payments.php');
    api_json(201, ['ok' => true, 'message' => 'Payment submitted for verification.']);
}

api_json(405, ['error' => 'Method not allowed.']);
