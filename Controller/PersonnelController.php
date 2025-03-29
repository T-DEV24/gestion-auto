<?php
require_once '../Config/connexion.php';

class PersonnelController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Gestion des requêtes (pour les actions comme delete)
    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deletePersonnel($_GET['id']);
            header('Location: ../Vue/listePersonnel.php?success=Personnel supprimé');
            exit();
        }
    }

    public function getAllPersonnel() {
        $stmt = $this->pdo->query("SELECT * FROM personnel");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPersonnel($nom, $prenom, $role, $email) {
        $stmt = $this->pdo->prepare("INSERT INTO personnel (nom, prenom, role, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $role, $email]);
    }

    public function updatePersonnel($id, $nom, $prenom, $role, $email) {
        $stmt = $this->pdo->prepare("UPDATE personnel SET nom = ?, prenom = ?, role = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $role, $email, $id]);
    }

    public function deletePersonnel($id) {
        $stmt = $this->pdo->prepare("DELETE FROM personnel WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getPersonnelById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM personnel WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Appel direct pour gérer les requêtes
if (basename($_SERVER['PHP_SELF']) === 'PersonnelController.php') {
    $controller = new PersonnelController();
    $controller->handleRequest();
}
?>