<?php
require_once '../Config/connexion.php';

class ApprenantController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Vérifier si un apprenant est déjà lié à un utilisateur
    public function isApprenantLinkedToUser($apprenant_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM apprenants WHERE id = ? AND user_id IS NOT NULL");
        $stmt->execute([$apprenant_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllApprenants() {
        $stmt = $this->pdo->query("SELECT a.*, u.username 
                                   FROM apprenants a 
                                   LEFT JOIN users u ON a.user_id = u.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addApprenant($nom, $prenom, $email, $user_id = null) {
        // Vérifier si l'email est vide
        if (empty($email)) {
            throw new Exception("L'email ne peut pas être vide.");
        }
    
        // Vérifier si l'email existe déjà
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM apprenants WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cet email est déjà utilisé.");
        }
    
        // Vérifier si user_id est déjà lié à un autre apprenant
        if ($user_id && $this->isUserLinkedToApprenant($user_id)) {
            throw new Exception("Cet utilisateur est déjà lié à un apprenant.");
        }
    
        // Insérer l'apprenant avec l'email
        $stmt = $this->pdo->prepare("INSERT INTO apprenants (nom, prenom, email, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $user_id]);
    }

    public function updateApprenant($id, $nom, $prenom, $user_id = null) {
        if ($user_id && $this->isUserLinkedToApprenant($user_id)) {
            $stmt = $this->pdo->prepare("SELECT user_id FROM apprenants WHERE id = ?");
            $stmt->execute([$id]);
            $current_user_id = $stmt->fetchColumn();
            if ($current_user_id != $user_id) {
                throw new Exception("Cet utilisateur est déjà lié à un autre apprenant.");
            }
        }
        $stmt = $this->pdo->prepare("UPDATE apprenants SET nom = ?, prenom = ?, user_id = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $user_id, $id]);
    }

    public function deleteApprenant($id) {
        $stmt = $this->pdo->prepare("DELETE FROM apprenants WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getApprenantById($id) {
        $stmt = $this->pdo->prepare("SELECT a.*, u.username 
                                     FROM apprenants a 
                                     LEFT JOIN users u ON a.user_id = u.id 
                                     WHERE a.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deleteApprenant($_GET['id']);
            header('Location: ../Vue/listeApprenant.php?success=Apprenant supprimé');
            exit();
        }
    }

    private function isUserLinkedToApprenant($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM apprenants WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }
}

if (basename($_SERVER['PHP_SELF']) === 'ApprenantController.php') {
    $controller = new ApprenantController();
    $controller->handleRequest();
}
?>