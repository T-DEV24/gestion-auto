<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/PaiementController.php';
$controller = new PaiementController();

// Gérer la recherche par code de facture
$searchCode = isset($_GET['search_code']) ? trim($_GET['search_code']) : '';
if ($searchCode) {
    $paiements = $controller->searchPaiementsByFactureCode($searchCode);
} else {
    $paiements = $controller->getAllPaiements();
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-file-invoice-dollar me-2"></i>Liste des paiements
    </h2>

    <!-- Formulaire de recherche par code de facture -->
    <form method="GET" action="listePaiement.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search_code" class="form-control" placeholder="Rechercher par code de facture (ex. FACT-2025-0001)" value="<?php echo htmlspecialchars($searchCode); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Rechercher
            </button>
        </div>
    </form>

    <!-- Afficher un message de succès ou d'erreur si présent -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tableau des paiements -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Code Facture</th> <!-- Nouvelle colonne -->
            <th>Utilisateur</th>
            <th>Formation</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Date de paiement</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($paiements)): ?>
            <tr>
                <td colspan="8" class="text-center">Aucun paiement trouvé.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($paiements as $paiement): ?>
                <tr>
                    <td><?php echo htmlspecialchars($paiement['id']); ?></td>
                    <td><?php echo htmlspecialchars($paiement['code_facture'] ?? 'N/A'); ?></td> <!-- Afficher le code -->
                    <td><?php echo htmlspecialchars($paiement['username']); ?></td>
                    <td><?php echo htmlspecialchars($paiement['formation_titre']); ?></td>
                    <td><?php echo htmlspecialchars($paiement['montant']); ?> EUR</td>
                    <td>
                        <?php 
                        if ($paiement['statut'] === 'payé') {
                            echo '<span class="badge bg-success">Payé</span>';
                        } elseif ($paiement['statut'] === 'à payer à la caisse') {
                            echo '<span class="badge bg-warning">À payer à la caisse</span>';
                        } else {
                            echo '<span class="badge bg-secondary">En attente</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($paiemzent['date_paiement'] ?? 'Non spécifiée'); ?></td>
                    <td>
                        <!-- Bouton Supprimer -->
                        <a href="../Controller/PaiementController.php?action=delete&id=<?php echo $paiement['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                        <!-- Bouton Valider le paiement -->
                        <?php if ($paiement['statut'] === 'à payer à la caisse'): ?>
                            <a href="../Controller/PaiementController.php?action=validate&id=<?php echo $paiement['id']; ?>"
                               class="btn btn-sm btn-success mt-1"
                               onclick="return confirm('Valider ce paiement ?');">
                                <i class="fas fa-check"></i> Valider
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="formulairePaiement.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter un paiement
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>