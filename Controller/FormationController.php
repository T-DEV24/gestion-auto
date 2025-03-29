<?php
require_once '../Config/connexion.php';

class FormationController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Gestion des requêtes
    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deleteFormation($_GET['id']);
            header('Location: ../Vue/listeFormation.php?success=Formation supprimée');
            exit();
        }
    }

    public function getAllFormations() {
        $stmt = $this->pdo->query("SELECT * FROM formations");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFormationById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM formations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFormation($titre, $description, $prix) {
        $stmt = $this->pdo->prepare("INSERT INTO formations (titre, description, prix) VALUES (?, ?, ?)");
        $stmt->execute([$titre, $description, $prix]);
    }

    public function updateFormation($id, $titre, $description, $prix) {
        $stmt = $this->pdo->prepare("UPDATE formations SET titre = ?, description = ?, prix = ? WHERE id = ?");
        $stmt->execute([$titre, $description, $prix, $id]);
    }

    public function deleteFormation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM formations WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Appel direct pour gérer les requêtes
if (basename($_SERVER['PHP_SELF']) === 'FormationController.php') {
    $controller = new FormationController();
    $controller->handleRequest();
}
?>