<?php
require_once '../Config/connexion.php';
require_once '../Config/auth.php';

class AuthController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'register':
                $this->register();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'me':
                $this->currentUser();
                break;
            default:
                header('Location: ../Vue/landing.php');
                exit();
        }
    }

    private function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ensureSessionStarted();
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if ($username === '' || $password === '') {
                header('Location: ../Vue/login.php?error=Nom d\'utilisateur ou mot de passe requis');
                exit();
            }

            if ($this->hasTooManyLoginAttempts()) {
                header('Location: ../Vue/login.php?error=Trop de tentatives. Réessayez plus tard.');
                exit();
            }

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $this->clearLoginAttempts();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $token = createJwt([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                ]);
                $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                setcookie('auth_token', $token, [
                    'expires' => time() + JWT_TTL_SECONDS,
                    'path' => '/',
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
                if ($user['role'] === 'admin') {
                    header('Location: ../Vue/dashboard.php');
                } else {
                    header('Location: ../Vue/main.php');
                }
                exit();
            } else {
                $this->recordLoginAttempt();
                header('Location: ../Vue/login.php?error=Nom d\'utilisateur ou mot de passe incorrect');
                exit();
            }
        }
    }

    private function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $roleInput = $_POST['role'] ?? 'user';

            if (empty($username)) {
                header('Location: ../Vue/register.php?error=Le nom d\'utilisateur est requis');
                exit();
            }

            if (empty($email)) {
                header('Location: ../Vue/register.php?error=L\'email est requis');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header('Location: ../Vue/register.php?error=Format d\'email invalide');
                exit();
            }

            if (empty($password)) {
                header('Location: ../Vue/register.php?error=Le mot de passe est requis');
                exit();
            }

            if (strlen($password) < 8) {
                header('Location: ../Vue/register.php?error=Le mot de passe doit contenir au moins 8 caractères');
                exit();
            }

            $allowedRoles = ['user', 'admin'];
            $role = 'user';
            if (isUserAdmin() && in_array($roleInput, $allowedRoles, true)) {
                $role = $roleInput;
            }

            $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                header('Location: ../Vue/register.php?error=Nom d\'utilisateur déjà pris');
                exit();
            }

            $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                header('Location: ../Vue/register.php?error=Email déjà utilisé');
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $role]);

            header('Location: ../Vue/login.php?success=Utilisateur créé avec succès');
            exit();
        }
    }

    private function logout() {
        ensureSessionStarted();
        $_SESSION = [];
        session_destroy();
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('auth_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: ../Vue/landing.php');
        exit();
    }

    private function currentUser() {
        ensureSessionStarted();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            exit();
        }

        echo json_encode([
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
        ]);
        exit();
    }

    private function hasTooManyLoginAttempts(): bool {
        $windowSeconds = 900;
        $maxAttempts = 5;
        $now = time();

        if (empty($_SESSION['login_attempts'])) {
            return false;
        }

        $_SESSION['login_attempts'] = array_filter(
            $_SESSION['login_attempts'],
            function ($timestamp) use ($now, $windowSeconds) {
                return ($now - $timestamp) <= $windowSeconds;
            }
        );

        return count($_SESSION['login_attempts']) >= $maxAttempts;
    }

    private function recordLoginAttempt(): void {
        if (empty($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        $_SESSION['login_attempts'][] = time();
    }

    private function clearLoginAttempts(): void {
        $_SESSION['login_attempts'] = [];
    }
}

$controller = new AuthController();
$controller->handleRequest();
?>
