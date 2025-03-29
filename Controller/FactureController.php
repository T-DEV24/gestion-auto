<?php
require_once '../Config/connexion.php';
require_once '../Vendor/fpdf/fpdf.php';

class FactureController {
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
            case 'generate':
                $this->generate();
                break;
            default:
                header('Location: ../Vue/listePaiement.php');
                exit();
        }
    }

    private function generate() {
        $paiement_id = $_GET['id'];

        // Récupérer les informations du paiement
        $stmt = $this->pdo->prepare("
            SELECT p.*, i.apprenant_id, i.formation_id, a.nom AS apprenant_nom, a.prenom AS apprenant_prenom, f.nom AS formation_nom
            FROM paiements p
            JOIN inscriptions i ON p.inscription_id = i.id
            JOIN apprenants a ON i.apprenant_id = a.id
            JOIN formations f ON i.formation_id = f.id
            WHERE p.id = ?
        ");
        $stmt->execute([$paiement_id]);
        $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$paiement) {
            die("Paiement non trouvé.");
        }

        // Créer le PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Facture', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Facture N°: ' . $paiement['id'], 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . $paiement['date_paiement'], 0, 1);
        $pdf->Ln(10);

        $pdf->Cell(0, 10, 'Apprenant: ' . $paiement['apprenant_nom'] . ' ' . $paiement['apprenant_prenom'], 0, 1);
        $pdf->Cell(0, 10, 'Formation: ' . $paiement['formation_nom'], 0, 1);
        $pdf->Cell(0, 10, 'Montant: ' . $paiement['montant'] . ' EUR', 0, 1);
        $pdf->Cell(0, 10, 'Méthode de paiement: ' . $paiement['methode_paiement'], 0, 1);

        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Merci pour votre paiement !', 0, 1, 'C');

        // Sauvegarder le PDF
        $filePath = '../Asset/templates/facture_' . $paiement['id'] . '.pdf';
        $pdf->Output('F', $filePath);

        // Rediriger vers la page de visualisation
        header('Location: ../Vue/voirFacture.php?file=' . urlencode('facture_' . $paiement['id'] . '.pdf'));
        exit();
    }
}

$controller = new FactureController();
$controller->handleRequest();
?>