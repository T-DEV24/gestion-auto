<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/ApprenantController.php';
$controller = new ApprenantController();
$apprenants = $controller->getAllApprenants();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-users me-2"></i>Liste des apprenants
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

    <!-- Tableau des apprenants -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Utilisateur lié</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($apprenants)): ?>
            <tr>
                <td colspan="5" class="text-center">Aucun apprenant trouvé.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($apprenants as $apprenant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($apprenant['id']); ?></td>
                    <td><?php echo htmlspecialchars($apprenant['nom']); ?></td>
                    <td><?php echo htmlspecialchars($apprenant['prenom']); ?></td>
                    <td>
                        <?php if ($apprenant['username']): ?>
                            <?php echo htmlspecialchars($apprenant['username']); ?>
                        <?php else: ?>
                            Aucun utilisateur lié
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="modifierApprenant.php?id=<?php echo $apprenant['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="../Controller/ApprenantController.php?action=delete&id=<?php echo $apprenant['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="formulaireApprenant.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter un apprenant
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>