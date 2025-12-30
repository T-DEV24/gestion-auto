<?php

if (!getenv('JWT_SECRET')) {
    throw new RuntimeException('JWT_SECRET manquant. Définissez une clé secrète forte.');
}
define('JWT_SECRET', getenv('JWT_SECRET'));
define('JWT_TTL_SECONDS', 60 * 60 * 4);
define('REFRESH_TOKEN_TTL_SECONDS', 60 * 60 * 24 * 14);

function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string
{
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

function createJwt(array $payload, ?int $expiresAt = null): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $expiresAt = $expiresAt ?? (time() + JWT_TTL_SECONDS);
    $payload['exp'] = $expiresAt;

    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);

    return $headerEncoded . '.' . $payloadEncoded . '.' . base64UrlEncode($signature);
}

function verifyJwt(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
    $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true);
    $signature = base64UrlDecode($signatureEncoded);

    if (!hash_equals($expectedSignature, $signature)) {
        return null;
    }

    $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    if (!is_array($payload)) {
        return null;
    }

    if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
        return null;
    }

    return $payload;
}
