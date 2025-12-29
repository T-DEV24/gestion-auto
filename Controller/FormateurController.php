<?php
require_once '../Config/connexion.php';

class FormateurController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getAllFormateurs() {
        $stmt = $this->pdo->query("SELECT f.*, u.username 
                                   FROM formateurs f 
                                   LEFT JOIN users u ON f.user_id = u.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addFormateur($nom, $prenom, $email, $specialite, $user_id = null) {
        if (empty($email)) {
            throw new Exception("L'email ne peut pas être vide.");
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM formateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cet email est déjà utilisé.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO formateurs (nom, prenom, email, specialite, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $specialite, $user_id]);
    }

    public function updateFormateur($id, $nom, $prenom, $email, $specialite, $user_id = null) {
        $stmt = $this->pdo->prepare("UPDATE formateurs SET nom = ?, prenom = ?, email = ?, specialite = ?, user_id = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $specialite, $user_id, $id]);
    }

    public function deleteFormateur($id) {
        $stmt = $this->pdo->prepare("DELETE FROM formateurs WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getFormateurById($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, u.username 
                                     FROM formateurs f 
                                     LEFT JOIN users u ON f.user_id = u.id 
                                     WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function assignApprenant($formateur_id, $apprenant_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM apprenant_formateur WHERE formateur_id = ? AND apprenant_id = ?");
        $stmt->execute([$formateur_id, $apprenant_id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cette association existe déjà.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO apprenant_formateur (formateur_id, apprenant_id) VALUES (?, ?)");
        $stmt->execute([$formateur_id, $apprenant_id]);
    }

    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deleteFormateur($_GET['id']);
            header('Location: ../Vue/listeFormateur.php?success=Formateur supprimé');
            exit();
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'FormateurController.php') {
    $controller = new FormateurController();
    $controller->handleRequest();
}
?>
