<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../Controller/InscriptionController.php';
$controller = new InscriptionController();
$user_id = $_SESSION['user_id'];
$formations = $controller->getAllFormations();
$inscriptions = $controller->getUserInscriptions($user_id);
$paiements = $controller->getUserPaiements($user_id);
$factures = $controller->getUserFactures($user_id);

if (isset($_GET['action']) && $_GET['action'] === 'register' && isset($_GET['formation_id'])) {
    $formation_id = $_GET['formation_id'];
    if ($controller->registerToFormation($user_id, $formation_id)) {
        header('Location: main.php?success=Inscription réussie');
        exit();
    } else {
        header('Location: main.php?error=Vous êtes déjà inscrit à cette formation');
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'pay' && isset($_GET['paiement_id']) && isset($_POST['methode_paiement'])) {
    $paiement_id = $_GET['paiement_id'];
    $methode_paiement = $_POST['methode_paiement'];
    $controller->payFacture($paiement_id, $methode_paiement);
    header('Location: main.php?success=Paiement effectué');
    exit();
}

ob_start();
?>

<div class="container mt-4">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <!-- Section des formations -->
    <h3 class="mb-3"><i class="fas fa-book me-2"></i>Formations disponibles</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($formations as $formation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($formation['titre']); ?></td>
                        <td><?php echo htmlspecialchars($formation['prix']); ?> EUR</td>
                        <td>
                            <a href="main.php?action=register&formation_id=<?php echo $formation['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle me-1"></i>S'inscrire
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Section des factures -->
<h3 class="mb-3 mt-4">
    <i class="fas fa-file-invoice me-2"></i>Vos factures
</h3>
<?php if (empty($factures)): ?>
    <p class="text-muted">Vous n'avez aucune facture.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Code Facture</th> <!-- Nouvelle colonne -->
                    <th scope="col">Formation</th>
                    <th scope="col">Montant</th>
                    <th scope="col">Date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($factures as $facture): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($facture['code_facture']); ?></td> <!-- Afficher le code -->
                        <td><?php echo htmlspecialchars($facture['titre']); ?></td>
                        <td><?php echo htmlspecialchars($facture['montant_total']); ?> EUR</td>
                        <td><?php echo htmlspecialchars($facture['date_facture']); ?></td>
                        <td>
                            <a href="../Factures/facture_<?php echo $facture['id']; ?>.pdf" class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-download me-1"></i>Télécharger
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

    <!-- Section des paiements -->
    <h3 class="mb-3 mt-4">
        <i class="fas fa-credit-card me-2"></i>Vos paiements
    </h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Formation</th>
                    <th scope="col">Montant</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($paiement['titre']); ?></td>
                        <td><?php echo htmlspecialchars($paiement['montant']); ?> EUR</td>
                        <td>
                            <?php if ($paiement['statut'] === 'payé'): ?>
                                <span class="badge bg-success">Payé</span>
                            <?php elseif ($paiement['statut'] === 'à payer à la caisse'): ?>
                                <span class="badge bg-warning">À payer à la caisse</span>
                            <?php else: ?>
                                <span class="badge bg-warning">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($paiement['statut'] === 'en attente'): ?>
                                <form method="POST" action="main.php?action=pay&paiement_id=<?php echo $paiement['id']; ?>" class="d-inline">
                                    <select name="methode_paiement" class="form-select d-inline w-auto" required>
                                        <option value="Espèces">Espèces</option>
                                        <option value="Carte">Carte</option>
                                        <option value="Mobile Money">Mobile Money</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmer le paiement ?');">
                                        <i class="fas fa-check-circle me-1"></i>Payer
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>