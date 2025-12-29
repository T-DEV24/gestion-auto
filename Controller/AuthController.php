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
            default:
                header('Location: ../Vue/landing.php');
                exit();
        }
    }

    private function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                if ($user['role'] === 'admin') {
                    header('Location: ../Vue/dashboard.php');
                } else {
                    header('Location: ../Vue/main.php');
                }
                exit();
            } else {
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

            if (empty($password)) {
                header('Location: ../Vue/register.php?error=Le mot de passe est requis');
                exit();
            }

            $allowedRoles = ['user', 'admin'];
            $role = 'user';
            if (isUserAdmin() && in_array($roleInput, $allowedRoles, true)) {
                $role = $roleInput;
            }

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                header('Location: ../Vue/register.php?error=Nom d\'utilisateur déjà pris');
                exit();
            }

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
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
        session_start();
        session_destroy();
        header('Location: ../Vue/landing.php');
        exit();
    }
}

$controller = new AuthController();
$controller->handleRequest();
?>
