<?php
declare(strict_types=1);

/** Envía correo transaccional mediante Resend sin dependencias externas. */
function sendNotificationEmail(
    string $to,
    string $subject,
    string $html,
    string $idempotencyKey
): array {
    $apiKey = trim((string)(getenv('RESEND_API_KEY') ?: ''));
    $from = trim((string)(getenv('MAIL_FROM') ?: ''));

    if ($apiKey === '' || $from === '') {
        return ['sent' => false, 'error' => 'Faltan RESEND_API_KEY o MAIL_FROM en Railway.'];
    }

    $payload = json_encode([
        'from' => $from,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'Idempotency-Key: ' . $idempotencyKey,
            ]),
            'content' => $payload,
            'ignore_errors' => true,
            'timeout' => 15,
        ],
    ]);

    $response = @file_get_contents('https://api.resend.com/emails', false, $context);
    $statusLine = $http_response_header[0] ?? '';
    $sent = preg_match('/\s2\d\d\s/', $statusLine) === 1;

    return [
        'sent' => $sent,
        'error' => $sent ? null : ($response ?: 'No fue posible contactar el servicio de correo.'),
    ];
}

function credentialToken(string $codigo): string
{
    $secret = (string)(getenv('CREDENTIAL_SECRET') ?: getenv('ADMIN_PASSWORD') ?: '');
    return hash_hmac('sha256', $codigo, $secret);
}
