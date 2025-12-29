<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/FormateurController.php';
require_once '../Controller/ApprenantController.php';
require_once '../Controller/ChatController.php';

$formateurController = new FormateurController();
$apprenantController = new ApprenantController();
$chatController = new ChatController();

$formateurs = $formateurController->getAllFormateurs();
$apprenants = $apprenantController->getAllApprenants();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = $_POST['type'];
        $nom = trim($_POST['name']);
        $formateur_id = (int) $_POST['formateur_id'];
        $apprenant_id = (int) $_POST['apprenant_id'];

        $apprenant = $apprenantController->getApprenantById($apprenant_id);
        $formateur = $formateurController->getFormateurById($formateur_id);
        $apprenantUserId = $apprenant['user_id'] ?? null;
        $formateurUserId = $formateur['user_id'] ?? null;

        if (!$apprenantUserId || !$formateurUserId) {
            throw new Exception("Les utilisateurs associés sont requis pour créer un chat.");
        }

        $chatName = $nom !== '' ? $nom : 'Chat Apprenant/Formateur';
        $chatController->createChat($type, $chatName, [(int) $apprenantUserId, (int) $formateurUserId]);
        header('Location: chat.php?success=Chat créé');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-comments me-2"></i>Créer un chat</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Type de chat</label>
            <select name="type" class="form-select" required>
                <option value="direct">Direct</option>
                <option value="groupe">Groupe</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nom du chat (optionnel)</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Formateur</label>
            <select name="formateur_id" class="form-select" required>
                <option value="">Sélectionner</option>
                <?php foreach ($formateurs as $formateur): ?>
                    <option value="<?php echo $formateur['id']; ?>">
                        <?php echo htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Apprenant</label>
            <select name="apprenant_id" class="form-select" required>
                <option value="">Sélectionner</option>
                <?php foreach ($apprenants as $apprenant): ?>
                    <option value="<?php echo $apprenant['id']; ?>">
                        <?php echo htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Créer
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
