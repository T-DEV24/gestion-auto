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

    // Gestion des requêtes (pour les actions comme delete et validate)
    public function handleRequest() {
        // Action pour supprimer un paiement
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            try {
                $this->deletePaiement($_GET['id']);
                header('Location: ../Vue/listePaiement.php?success=Paiement supprimé avec succès');
                exit();
            } catch (Exception $e) {
                header('Location: ../Vue/listePaiement.php?error=Erreur lors de la suppression du paiement : ' . urlencode($e->getMessage()));
                exit();
            }
        }

        // Action pour valider un paiement
        if (isset($_GET['action']) && $_GET['action'] === 'validate' && isset($_GET['id'])) {
            try {
                $this->validatePaiement($_GET['id']);
                header('Location: ../Vue/listePaiement.php?success=Paiement validé avec succès');
                exit();
            } catch (Exception $e) {
                header('Location: ../Vue/listePaiement.php?error=Erreur lors de la validation du paiement : ' . urlencode($e->getMessage()));
                exit();
            }
        }

        // Si aucune action valide n'est trouvée, rediriger avec un message d'erreur
        header('Location: ../Vue/listePaiement.php?error=Aucune action valide spécifiée');
        exit();
    }

    // Récupérer tous les paiements avec le code de facture
    public function getAllPaiements() {
        $stmt = $this->pdo->query("
            SELECT p.*, u.username, f.titre AS formation_titre, fa.code_facture
            FROM paiements p
            JOIN users u ON p.user_id = u.id
            JOIN formations f ON p.formation_id = f.id
            LEFT JOIN apprenants a ON a.user_id = p.user_id
            LEFT JOIN factures fa ON fa.apprenant_id = a.id AND fa.montant_total = p.montant
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rechercher des paiements par code de facture
    public function searchPaiementsByFactureCode($code_facture) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.username, f.titre AS formation_titre, fa.code_facture
            FROM paiements p
            JOIN users u ON p.user_id = u.id
            JOIN formations f ON p.formation_id = f.id
            JOIN apprenants a ON a.user_id = p.user_id
            JOIN factures fa ON fa.apprenant_id = a.id AND fa.montant_total = p.montant
            WHERE fa.code_facture = ?
        ");
        $stmt->execute([$code_facture]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un paiement
    public function addPaiement($user_id, $formation_id, $montant) {
        $stmt = $this->pdo->prepare("INSERT INTO paiements (user_id, formation_id, montant, statut) VALUES (?, ?, ?, 'en attente')");
        $result = $stmt->execute([$user_id, $formation_id, $montant]);
        if (!$result) {
            throw new Exception("Échec de l'ajout du paiement.");
        }
    }

    // Supprimer un paiement
    public function deletePaiement($id) {
        $stmt = $this->pdo->prepare("DELETE FROM paiements WHERE id = ?");
        $result = $stmt->execute([$id]);
        if (!$result || $stmt->rowCount() === 0) {
            throw new Exception("Le paiement avec l'ID $id n'a pas pu être supprimé ou n'existe pas.");
        }
    }

    // Récupérer un paiement par ID avec le code de facture
    public function getPaiementById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.username, f.titre AS formation_titre, fa.code_facture
            FROM paiements p
            JOIN users u ON p.user_id = u.id
            JOIN formations f ON p.formation_id = f.id
            LEFT JOIN apprenants a ON a.user_id = p.user_id
            LEFT JOIN factures fa ON fa.apprenant_id = a.id AND fa.montant_total = p.montant
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $paiement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$paiement) {
            throw new Exception("Le paiement avec l'ID $id n'existe pas.");
        }
        return $paiement;
    }

    // Valider un paiement
    public function validatePaiement($id) {
        // Vérifier si le paiement existe et a le statut 'à payer à la caisse'
        $paiement = $this->getPaiementById($id);
        if ($paiement['statut'] !== 'à payer à la caisse') {
            throw new Exception("Le paiement avec l'ID $id n'est pas en attente de validation (statut actuel : {$paiement['statut']}).");
        }

        // Mettre à jour le statut du paiement à 'payé'
        $stmt = $this->pdo->prepare("UPDATE paiements SET statut = 'payé', date_paiement = NOW() WHERE id = ?");
        $result = $stmt->execute([$id]);

        if (!$result || $stmt->rowCount() === 0) {
            throw new Exception("Échec de la validation du paiement avec l'ID $id.");
        }

        // Retourner un message de succès
        return "Paiement avec l'ID $id validé avec succès.";
        }
    }

// Appel direct pour gérer les requêtes
if (basename($_SERVER['PHP_SELF']) === 'PaiementController.php') {
    $controller = new PaiementController();
    $controller->handleRequest();
}
?>