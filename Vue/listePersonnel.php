<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/PersonnelController.php';
$controller = new PersonnelController();
$personnels = $controller->getAllPersonnel();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-id-card me-2"></i>Liste du personnel
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

    <!-- Tableau du personnel -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Rôle</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($personnels)): ?>
            <tr>
                <td colspan="6" class="text-center">Aucun personnel trouvé.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($personnels as $personnel): ?>
                <tr>
                    <td><?php echo htmlspecialchars($personnel['id']); ?></td>
                    <td><?php echo htmlspecialchars($personnel['nom']); ?></td>
                    <td><?php echo htmlspecialchars($personnel['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($personnel['role']); ?></td>
                    <td><?php echo htmlspecialchars($personnel['email']); ?></td>
                    <td>
                        <a href="modifierPersonnel.php?id=<?php echo $personnel['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="../Controller/PersonnelController.php?action=delete&id=<?php echo $personnel['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce personnel ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="formulairePersonnel.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter un personnel
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
