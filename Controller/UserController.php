<?php
require_once '../Config/connexion.php';

class UserController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Vérifier si un utilisateur est déjà lié à un apprenant
    public function isUserLinkedToApprenant($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM apprenants WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT u.*, a.nom AS apprenant_nom, a.prenom AS apprenant_prenom 
                                   FROM users u 
                                   LEFT JOIN apprenants a ON u.id = a.user_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser($username, $email, $password, $role) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
    }

    public function updateUser($id, $username, $email, $role) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT u.*, a.nom AS apprenant_nom, a.prenom AS apprenant_prenom 
                                     FROM users u 
                                     LEFT JOIN apprenants a ON u.id = a.user_id 
                                     WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deleteUser($_GET['id']);
            header('Location: ../Vue/listeUtilisateur.php?success=Utilisateur supprimé');
            exit();
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'UserController.php') {
    $controller = new UserController();
    $controller->handleRequest();
}
?>