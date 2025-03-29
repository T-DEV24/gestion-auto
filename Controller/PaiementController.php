<?php
require_once '../Config/connexion.php';

class PaiementController {
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
            $this->deletePaiement($_GET['id']);
            header('Location: ../Vue/listePaiement.php?success=Paiement supprimé');
            exit();
        }
    }

    public function getAllPaiements() {
        $stmt = $this->pdo->query("SELECT p.*, u.username, f.titre AS formation_titre FROM paiements p JOIN users u ON p.user_id = u.id JOIN formations f ON p.formation_id = f.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPaiement($user_id, $formation_id, $montant) {
        $stmt = $this->pdo->prepare("INSERT INTO paiements (user_id, formation_id, montant, statut) VALUES (?, ?, ?, 'en attente')");
        $stmt->execute([$user_id, $formation_id, $montant]);
    }

    public function deletePaiement($id) {
        $stmt = $this->pdo->prepare("DELETE FROM paiements WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getPaiementById($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username, f.titre AS formation_titre FROM paiements p JOIN users u ON p.user_id = u.id JOIN formations f ON p.formation_id = f.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Appel direct pour gérer les requêtes
if (basename($_SERVER['PHP_SELF']) === 'PaiementController.php') {
    $controller = new PaiementController();
    $controller->handleRequest();
}
?>