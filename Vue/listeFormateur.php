<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/FormateurController.php';
$controller = new FormateurController();
$formateurs = $controller->getAllFormateurs();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-chalkboard-teacher me-2"></i>Liste des formateurs</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Spécialité</th>
                <th>Utilisateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($formateurs as $formateur): ?>
            <tr>
                <td><?php echo htmlspecialchars($formateur['id']); ?></td>
                <td><?php echo htmlspecialchars($formateur['nom']); ?></td>
                <td><?php echo htmlspecialchars($formateur['prenom']); ?></td>
                <td><?php echo htmlspecialchars($formateur['email']); ?></td>
                <td><?php echo htmlspecialchars($formateur['specialite'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($formateur['username'] ?? ''); ?></td>
                <td>
                    <a class="btn btn-sm btn-danger" href="../Controller/FormateurController.php?action=delete&id=<?php echo $formateur['id']; ?>" onclick="return confirm('Supprimer ce formateur ?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="assignerFormateur.php" class="btn btn-secondary mt-3">
        <i class="fas fa-link me-2"></i>Associer un formateur à un apprenant
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
