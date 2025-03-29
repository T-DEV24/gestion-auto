<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/UserController.php';
$controller = new UserController();
$users = $controller->getAllUsers();

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-user-shield me-2"></i>Liste des utilisateurs
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

    <!-- Tableau des utilisateurs -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Apprenant lié</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="6" class="text-center">Aucun utilisateur trouvé.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <?php if ($user['apprenant_nom']): ?>
                            <?php echo htmlspecialchars($user['apprenant_nom'] . ' ' . $user['apprenant_prenom']); ?>
                        <?php else: ?>
                            Aucun apprenant lié
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="modifierUtilisateur.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="../Controller/UserController.php?action=delete&id=<?php echo $user['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="formulaireUtilisateur.php" class="btn btn-primary mt-3">
        <i class="fas fa-plus-circle me-2"></i>Ajouter un utilisateur
    </a>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>