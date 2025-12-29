<?php
require_once '../Config/auth.php';
requireAdmin('login.php');

require_once '../Controller/FormationController.php';
$controller = new FormationController();
$formations = $controller->getAllFormations();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-book me-2"></i>Liste des formations
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

    <!-- Tableau des formations -->
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($formations)): ?>
                <tr>
                    <td colspan="5" class="text-center">Aucune formation trouvée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($formations as $formation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($formation['id']); ?></td>
                        <td><?php echo htmlspecialchars($formation['titre']); ?></td>
                        <td><?php echo htmlspecialchars($formation['description']); ?></td>
                        <td><?php echo htmlspecialchars($formation['prix']); ?> EUR</td>
                        <td>
                            <a href="modifierFormation.php?id=<?php echo $formation['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="../Controller/FormationController.php?action=delete&id=<?php echo $formation['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Bouton pour ajouter une nouvelle formation -->
    <a href="formulaireFormation.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter une formation
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
