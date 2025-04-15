<?php
ob_start(); // Démarrer le buffering de sortie
require_once '../Config/connexion.php';
require_once '../vendor/fpdf186/fpdf.php';

// Extend FPDF to add RoundedRect method
class CustomFPDF extends FPDF {
    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F') {
            $op = 'f';
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }
}

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

    // Générer un code unique pour la facture
    private function generateUniqueCode() {
        $year = date('Y'); // Année actuelle (ex. 2025)
        $prefix = "FACT-$year-"; // Préfixe du code (ex. FACT-2025-)

        // Trouver le dernier numéro utilisé pour cette année
        $stmt = $this->pdo->prepare("SELECT code_facture FROM factures WHERE code_facture LIKE ? ORDER BY code_facture DESC LIMIT 1");
        $stmt->execute(["$prefix%"]);
        $lastCode = $stmt->fetchColumn();

        if ($lastCode) {
            // Extraire le numéro de la dernière facture (ex. FACT-2025-0001 -> 0001)
            $lastNumber = (int) substr($lastCode, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1; // Première facture de l'année
        }

        // Formater le numéro avec 4 chiffres (ex. 0001, 0002, etc.)
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        return $prefix . $formattedNumber; // Ex. FACT-2025-0001
    }

    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            try {
                $this->deleteInscription($_GET['id']);
                header('Location: ../Vue/listeInscription.php?success=Inscription supprimée avec succès');
                exit();
            } catch (Exception $e) {
                header('Location: ../Vue/listeInscription.php?error=Erreur lors de la suppression : ' . urlencode($e->getMessage()));
                exit();
            }
        }
        // Redirection par défaut si aucune action valide
        header('Location: ../Vue/main.php?error=Aucune action valide spécifiée');
        exit();
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
            SELECT f.id, f.code_facture, f.montant_total, f.date_facture, fo.titre
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
        $result = $stmt->execute([$id]);
        if (!$result || $stmt->rowCount() === 0) {
            throw new Exception("L'inscription avec l'ID $id n'a pas pu être supprimée ou n'existe pas.");
        }
    }

    public function payFacture($paiement_id, $methode_paiement = 'Espèces') {
        // Déterminer le statut en fonction de la méthode de paiement
        $statut = ($methode_paiement === 'Espèces') ? 'à payer à la caisse' : 'payé';

        // Mettre à jour le statut du paiement
        $stmt = $this->pdo->prepare("UPDATE paiements SET statut = ?, date_paiement = NOW() WHERE id = ?");
        $result = $stmt->execute([$statut, $paiement_id]);
        if (!$result || $stmt->rowCount() === 0) {
            throw new Exception("Échec de la mise à jour du statut du paiement avec l'ID $paiement_id.");
        }

        // Récupérer les informations pour la facture
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
            // Générer un code unique pour la facture
            $code_facture = $this->generateUniqueCode();

            // Insérer la facture dans la base de données avec le code unique
            $stmt = $this->pdo->prepare("INSERT INTO factures (apprenant_id, montant_total, date_facture, code_facture) VALUES (?, ?, CURDATE(), ?)");
            $stmt->execute([$paiement['apprenant_id'], $paiement['montant'], $code_facture]);
            $facture_id = $this->pdo->lastInsertId();

            // Générer le PDF de la facture avec le statut approprié et le code
            $this->generateFacturePDF(
                $facture_id,
                $paiement['nom'],
                $paiement['prenom'],
                $paiement['email'],
                $paiement['titre'],
                $paiement['montant'],
                $methode_paiement,
                $statut,
                $code_facture // Passer le code au générateur de PDF
            );
        } else {
            throw new Exception("Aucune information trouvée pour le paiement avec l'ID $paiement_id.");
        }
    }

    private function roundImageCorners($sourcePath, $radius) {
        $source = imagecreatefrompng($sourcePath);
        $width = imagesx($source);
        $height = imagesy($source);
    
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
    
        $mask = imagecreatetruecolor($width, $height);
        $black = imagecolorallocate($mask, 0, 0, 0);
        $white = imagecolorallocate($mask, 255, 255, 255);
        imagefill($mask, 0, 0, $black);
    
        imagefilledrectangle($mask, 0, 0, $width, $height, $white);
        imagefilledellipse($mask, $radius, $radius, $radius * 2, $radius * 2, $black);
        imagefilledellipse($mask, $width - $radius, $radius, $radius * 2, $radius * 2, $black);
        imagefilledellipse($mask, $radius, $height - $radius, $radius * 2, $radius * 2, $black);
        imagefilledellipse($mask, $width - $radius, $height - $radius, $radius * 2, $radius * 2, $black);
    
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $maskPixel = imagecolorat($mask, $x, $y);
                if ($maskPixel == $black) {
                    imagesetpixel($image, $x, $y, $transparent);
                } else {
                    $sourcePixel = imagecolorat($source, $x, $y);
                    imagesetpixel($image, $x, $y, $sourcePixel);
                }
            }
        }
    
        $tempPath = __DIR__ . '/../signatures/logo_rounded.png';
        imagepng($image, $tempPath);
    
        imagedestroy($source);
        imagedestroy($mask);
        imagedestroy($image);
    
        return $tempPath;
    }

    private function generateFacturePDF($facture_id, $nom, $prenom, $email, $titre_formation, $montant, $methode_paiement, $statut, $code_facture) {
        $pdf = new CustomFPDF(); // Use the extended class
        $pdf->AddPage();

        // Définir les marges
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Couleurs personnalisées (R, G, B)
        $bleuFonce = [0, 51, 102]; // Couleur principale (titres, bordures)
        $bleuClair = [230, 240, 255]; // Fond des sections
        $vertSucces = [34, 139, 34]; // Statut "Payé"
        $orangeAttente = [255, 140, 0]; // Statut "En attente"
        $grisBordure = [180, 180, 180]; // Bordures légères
        $grisTexte = [100, 100, 100]; // Texte secondaire

        // --- Filigrane (optionnel) ---
        $pdf->SetFont('Arial', 'B', 50);
        $pdf->SetTextColor(200, 200, 200);
        $pdf->SetXY(30, 140); // Positionner le filigrane au centre de la page
        $pdf->Cell(0, 10, 'FACTURE', 0, 1, 'C');

        // --- Cadre global autour de la facture ---
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(10, 10, 190, 277); // Cadre global (A4 : 210x297 mm, marges de 10 mm)

        // --- En-tête de la facture ---
        // Logo centré
        $logoPath = __DIR__ . '/../signatures/logo.png';
        if (file_exists($logoPath)) {
            $logoWidth = 50; // Largeur réduite (précédemment 60)
            $logoHeight = 16; // Hauteur réduite (précédemment 20, proportion maintenue)
            $pageWidth = 210; // Largeur de la page A4 en mm
            $xLogoPosition = ($pageWidth - $logoWidth) / 2; // Centrer le logo
            $yLogoPosition = 15; // Position Y

            // Dessiner un cadre arrondi autour du logo
            $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFillColor(255, 255, 255); // Fond blanc pour le logo
            $pdf->RoundedRect($xLogoPosition - 2, $yLogoPosition - 2, $logoWidth + 4, $logoHeight + 4, 5, 'D'); // Cadre arrondi

            // Ajouter le logo
            $pdf->Image($logoPath, $xLogoPosition, $yLogoPosition, $logoWidth, $logoHeight);
        } else {
            // Si le logo n'existe pas, afficher un message
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTextColor($grisTexte[0], $grisTexte[1], $grisTexte[2]);
            $pageWidth = 210;
            $xPosition = ($pageWidth - 60) / 2; // Centrer le message
            $pdf->SetXY($xPosition, 15);
            $pdf->Cell(60, 10, '(Logo non disponible)', 0, 0, 'C');
        }

        // Titre "FACTURE" et code facture à droite
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetXY(15, 15);
        $pdf->Cell(180, 10, 'FACTURE', 0, 1, 'R'); // Titre à droite

        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor($grisTexte[0], $grisTexte[1], $grisTexte[2]);
        $pdf->SetXY(15, 25);
        $pdf->Cell(180, 6, 'Code Facture : ' . $code_facture, 0, 1, 'R');

        // Ligne de séparation sous l'en-tête
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 35, 195, 35);
        $pdf->Ln(25);

        // --- Informations de l'auto-école ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
        $pdf->SetDrawColor($grisBordure[0], $grisBordure[1], $grisBordure[2]);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(0, 8, 'AUTO-ECOLE GESTION', 'LTR', 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 6, 'Adresse : Tradex Tsinga, Vers rond point Nkoa Ebemda', 'LR', 1, 'L');
        $pdf->Cell(0, 6, 'Email : tcheuatatcheudjoclotaire@gmail.com', 'LBR', 1, 'L');
        $pdf->Ln(10);

        // --- Informations du client ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
        $pdf->Cell(0, 8, 'INFORMATIONS DU CLIENT', 'LTR', 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Nom : ' . $nom . ' ' . $prenom, 'LR', 1, 'L');
        $pdf->Cell(0, 6, 'Email : ' . $email, 'LBR', 1, 'L');
        $pdf->Ln(10);

        // --- Détails de la facture (sous forme de tableau) ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor($bleuClair[0], $bleuClair[1], $bleuClair[2]);
        $pdf->Cell(0, 8, 'DETAILS DE LA FACTURE', 'LTR', 1, 'L', true);

        // Tableau des détails
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(90, 8, 'Description', 'LTR', 0, 'L', true);
        $pdf->Cell(90, 8, 'Valeur', 'LTR', 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(90, 6, 'Formation', 'LR', 0, 'L', true);
        $pdf->Cell(90, 6, $titre_formation, 'LR', 1, 'L', true);

        $pdf->Cell(90, 6, 'Montant', 'LR', 0, 'L', true);
        $pdf->Cell(90, 6, $montant . ' EUR', 'LR', 1, 'L', true);

        $pdf->Cell(90, 6, 'Date de paiement', 'LR', 0, 'L', true);
        $pdf->Cell(90, 6, date('Y-m-d'), 'LR', 1, 'L', true);

        $pdf->Cell(90, 6, 'Methode de paiement', 'LR', 0, 'L', true);
        $pdf->Cell(90, 6, $methode_paiement, 'LR', 1, 'L', true);

        // Statut conditionnel
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 6, 'Statut', 'LR', 0, 'L', true);
        if ($statut === 'payé') {
            $pdf->SetTextColor($vertSucces[0], $vertSucces[1], $vertSucces[2]);
            $pdf->Cell(90, 6, 'Paye avec succes', 'LR', 1, 'L', true);
        } else {
            $pdf->SetTextColor($orangeAttente[0], $orangeAttente[1], $orangeAttente[2]);
            $pdf->Cell(90, 6, 'A payer a la caisse', 'LR', 1, 'L', true);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(90, 0, '', 'LBR');
        $pdf->Cell(90, 0, '', 'LBR', 1);
        $pdf->Ln(15);

        // --- Signatures (Auto-École et Apprenant) ---
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetX(15);
        $pdf->Cell(90, 10, 'Signature Auto-Ecole', 0, 0, 'C');
        $pdf->SetX(105);
        $pdf->Cell(90, 10, 'Signature Apprenant', 0, 1, 'C');

        // Ligne décorative sous les titres
        $pdf->SetDrawColor($bleuFonce[0], $bleuFonce[1], $bleuFonce[2]);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(35, $pdf->GetY() + 2, 85, $pdf->GetY() + 2); // Sous "Signature Auto-École"
        $pdf->Line(125, $pdf->GetY() + 2, 175, $pdf->GetY() + 2); // Sous "Signature Apprenant"
        $pdf->Ln(10);

        // --- Signature Auto-École ---
        $signaturePath = __DIR__ . '/../signatures/admin_signature.png';
        $xPosition = 15 + (90 - 60) / 2; // Centrer dans la colonne gauche
        $yPosition = $pdf->GetY();

        if (file_exists($signaturePath)) {
            $pdf->SetDrawColor($grisBordure[0], $grisBordure[1], $grisBordure[2]);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect($xPosition - 5, $yPosition - 5, 60 + 10, 25 + 10);
            $pdf->Image($signaturePath, $xPosition, $yPosition, 60, 25);
        } else {
            $pdf->SetDrawColor($grisBordure[0], $grisBordure[1], $grisBordure[2]);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect($xPosition - 5, $yPosition - 5, 60 + 10, 25 + 10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetTextColor($grisTexte[0], $grisTexte[1], $grisTexte[2]);
            $pdf->SetXY($xPosition, $yPosition + 10);
            $pdf->Cell(60, 6, '(Signature non disponible)', 0, 0, 'C');
        }

        // --- Signature Apprenant (espace vide) ---
        $xPositionApprenant = 105 + (90 - 60) / 2; // Centrer dans la colonne droite
        $pdf->SetDrawColor($grisBordure[0], $grisBordure[1], $grisBordure[2]);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect($xPositionApprenant - 5, $yPosition - 5, 60 + 10, 25 + 10);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor($grisTexte[0], $grisTexte[1], $grisTexte[2]);
        $pdf->SetXY($xPositionApprenant, $yPosition + 10);
        $pdf->Cell(60, 6, '(Espace pour signature)', 0, 0, 'C');

        $pdf->Ln(40); // Espace après les signatures

        // --- Pied de page ---
        $pdf->SetY(-25);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor($grisTexte[0], $grisTexte[1], $grisTexte[2]);
        $pdf->Cell(0, 6, 'Facture generee le ' . date('Y-m-d H:i:s') . ' | Page ' . $pdf->PageNo(), 0, 1, 'C');
        $pdf->Cell(0, 6, 'Auto-Ecole Gestion - Tous droits reserves', 0, 1, 'C');

        // Créer le dossier Factures et sauvegarder le PDF
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

ob_end_flush(); // Terminer le buffering de sortie
?>