<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../Controller/InscriptionController.php';
$controller = new InscriptionController();
$inscriptions = $controller->getAllInscriptions();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-clipboard-check me-2"></i>Liste des inscriptions
    </h2>

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

    <!-- Tableau des inscriptions -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Apprenant</th>
            <th>Formation</th>
            <th>Date d'inscription</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($inscriptions)): ?>
            <tr>
                <td colspan="5" class="text-center">Aucune inscription trouvée.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($inscriptions as $inscription): ?>
                <tr>
                    <td><?php echo htmlspecialchars($inscription['id']); ?></td>
                    <td>
                        <?php
                        $apprenantNom = isset($inscription['apprenant_nom']) ? htmlspecialchars($inscription['apprenant_nom']) : 'Inconnu';
                        $apprenantPrenom = isset($inscription['apprenant_prenom']) ? htmlspecialchars($inscription['apprenant_prenom']) : '';
                        echo $apprenantNom . ' ' . $apprenantPrenom;
                        ?>
                    </td>
                    <td><?php echo isset($inscription['formation_titre']) ? htmlspecialchars($inscription['formation_titre']) : 'Inconnue'; ?></td>
                    <td><?php echo htmlspecialchars($inscription['date_inscription']); ?></td>
                    <td>
                        <a href="modifierInscription.php?id=<?php echo $inscription['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="../Controller/InscriptionController.php?action=delete&id=<?php echo $inscription['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="formulaireInscription.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter une inscription
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>