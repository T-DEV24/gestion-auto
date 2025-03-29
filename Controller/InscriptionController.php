<?php
require_once '../Config/connexion.php';
require_once '../vendor/fpdf186/fpdf.php';

class InscriptionController {
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
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->deleteInscription($_GET['id']);
            header('Location: ../Vue/listeInscription.php?success=Inscription supprimée');
            exit();
        }
    }

    public function getAllFormations() {
        $stmt = $this->pdo->query("SELECT * FROM formations");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllInscriptions() {
        $stmt = $this->pdo->prepare("
            SELECT i.id, i.user_id, i.formation_id, i.date_inscription,
                   a.nom AS apprenant_nom, a.prenom AS apprenant_prenom,
                   f.titre AS formation_titre
            FROM inscriptions i
            LEFT JOIN apprenants a ON i.user_id = a.user_id
            LEFT JOIN formations f ON i.formation_id = f.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserInscriptions($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT f.titre, i.formation_id 
            FROM inscriptions i 
            JOIN formations f ON i.formation_id = f.id 
            WHERE i.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPaiements($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.montant, p.statut, f.titre 
            FROM paiements p 
            JOIN formations f ON p.formation_id = f.id 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserFactures($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT f.id, f.montant_total, f.date_facture, fo.titre
            FROM factures f
            JOIN apprenants a ON f.apprenant_id = a.id
            JOIN paiements p ON p.user_id = a.user_id AND p.montant = f.montant_total
            JOIN formations fo ON p.formation_id = fo.id
            WHERE a.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInscriptionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.id, i.user_id, i.formation_id, i.date_inscription,
                   a.nom AS apprenant_nom, a.prenom AS apprenant_prenom,
                   f.titre AS formation_titre
            FROM inscriptions i
            LEFT JOIN apprenants a ON i.user_id = a.user_id
            LEFT JOIN formations f ON i.formation_id = f.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registerToFormation($user_id, $formation_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscriptions WHERE user_id = ? AND formation_id = ?");
        $stmt->execute([$user_id, $formation_id]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->pdo->prepare("INSERT INTO inscriptions (user_id, formation_id, date_inscription) VALUES (?, ?, CURDATE())");
        $stmt->execute([$user_id, $formation_id]);

        $stmt = $this->pdo->prepare("SELECT prix, titre FROM formations WHERE id = ?");
        $stmt->execute([$formation_id]);
        $formation = $stmt->fetch();
        if (!$formation) {
            throw new Exception("Formation introuvable.");
        }
        $prix = $formation['prix'];

        $stmt = $this->pdo->prepare("INSERT INTO paiements (user_id, formation_id, montant, statut) VALUES (?, ?, ?, 'en attente')");
        $stmt->execute([$user_id, $formation_id, $prix]);

        return true;
    }

    public function updateInscription($id, $user_id, $formation_id) {
        $stmt = $this->pdo->prepare("UPDATE inscriptions SET user_id = ?, formation_id = ? WHERE id = ?");
        $stmt->execute([$user_id, $formation_id, $id]);
    }

    public function deleteInscription($id) {
        $stmt = $this->pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function payFacture($paiement_id, $methode_paiement = 'Espèces') {
        $stmt = $this->pdo->prepare("UPDATE paiements SET statut = 'payé', date_paiement = NOW() WHERE id = ?");
        $stmt->execute([$paiement_id]);

        $stmt = $this->pdo->prepare("
            SELECT p.user_id, p.montant, f.titre, a.id AS apprenant_id, a.nom, a.prenom, a.email
            FROM paiements p
            JOIN formations f ON p.formation_id = f.id
            JOIN apprenants a ON a.user_id = p.user_id
            WHERE p.id = ?
        ");
        $stmt->execute([$paiement_id]);
        $paiement = $stmt->fetch();

        if ($paiement) {
            $stmt = $this->pdo->prepare("INSERT INTO factures (apprenant_id, montant_total, date_facture) VALUES (?, ?, CURDATE())");
            $stmt->execute([$paiement['apprenant_id'], $paiement['montant']]);
            $facture_id = $this->pdo->lastInsertId();

            $this->generateFacturePDF(
                $facture_id,
                $paiement['nom'],
                $paiement['prenom'],
                $paiement['email'],
                $paiement['titre'],
                $paiement['montant'],
                $methode_paiement
            );
        }
    }

    private function generateFacturePDF($facture_id, $nom, $prenom, $email, $titre_formation, $montant, $methode_paiement) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, 'Facture N_' . $facture_id, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Auto-Ecole Gestion', 0, 1);
        $pdf->Cell(0, 10, 'Adresse : Tradex Tsinga, Vers rond point Nkoa Ebemda', 0, 1);
        $pdf->Cell(0, 10, 'Email : tcheuatatcheudjoclotaire@gmail.com', 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Client :', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nom : ' . $nom . ' ' . $prenom, 0, 1);
        $pdf->Cell(0, 10, 'Email : ' . $email, 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Details de la facture :', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Formation : ' . $titre_formation, 0, 1);
        $pdf->Cell(0, 10, 'Montant : ' . $montant . ' EUR', 0, 1);
        $pdf->Cell(0, 10, 'Date de paiement : ' . date('Y-m-d'), 0, 1);
        $pdf->Cell(0, 10, 'Methode de paiement : ' . $methode_paiement, 0, 1);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(0, 10, 'Statut : Paye avec succes', 0, 1);
        $pdf->SetTextColor(0, 0, 0);

        $facturesDir = __DIR__ . '/../Factures/';
        $facturePath = $facturesDir . 'facture_' . $facture_id . '.pdf';

        if (!is_dir($facturesDir)) {
            mkdir($facturesDir, 0777, true);
        }

        $pdf->Output('F', $facturePath);
    }
}

if (basename($_SERVER['PHP_SELF']) === 'InscriptionController.php') {
    $controller = new InscriptionController();
    $controller->handleRequest();
}
?>