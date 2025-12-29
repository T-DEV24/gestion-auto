<?php
require_once __DIR__ . '/jwt.php';

/**
 * Démarre la session si elle n'est pas déjà active.
 */
function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id']) && isset($_COOKIE['auth_token'])) {
        $payload = verifyJwt($_COOKIE['auth_token']);
        if ($payload && isset($payload['user_id'], $payload['username'], $payload['role'])) {
            $_SESSION['user_id'] = $payload['user_id'];
            $_SESSION['username'] = $payload['username'];
            $_SESSION['role'] = $payload['role'];
        } else {
            $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            setcookie('auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }
}

/**
 * Vérifie si l'utilisateur connecté possède le rôle administrateur.
 */
function isUserAdmin(): bool
{
    ensureSessionStarted();

    return isset($_SESSION['user_id']) && ($_SESSION['role'] ?? null) === 'admin';
}

/**
 * Redirige l'utilisateur si la session n'est pas valide ou si le rôle n'est pas admin.
 */
function requireAdmin(string $redirectIfNotAdmin = 'main.php', ?string $redirectIfNotAuthenticated = null): void
{
    ensureSessionStarted();

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . ($redirectIfNotAuthenticated ?? $redirectIfNotAdmin));
        exit();
    }

    if (($_SESSION['role'] ?? null) !== 'admin') {
        header('Location: ' . $redirectIfNotAdmin);
        exit();
    }
}
