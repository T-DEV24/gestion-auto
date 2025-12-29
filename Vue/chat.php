<?php
require_once '../Config/auth.php';
requireLogin();

require_once '../Controller/ChatController.php';
$controller = new ChatController();
$error = null;
$success = $_GET['success'] ?? null;

$chatId = isset($_GET['chat_id']) ? (int) $_GET['chat_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $chatId = (int) $_POST['chat_id'];
        $message = trim($_POST['message']);
        if ($message !== '') {
            $controller->addMessage($chatId, $_SESSION['user_id'], $message);
            header('Location: chat.php?chat_id=' . $chatId);
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$chats = $controller->getChatsForUser($_SESSION['user_id']);
$messages = $chatId ? $controller->getChatMessages($chatId) : [];
$participants = $chatId ? $controller->getChatParticipants($chatId) : [];

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-comments me-2"></i>Messagerie</h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="list-group">
                <?php foreach ($chats as $chat): ?>
                    <a href="chat.php?chat_id=<?php echo $chat['id']; ?>" class="list-group-item list-group-item-action<?php echo $chatId === (int) $chat['id'] ? ' active' : ''; ?>">
                        <?php echo htmlspecialchars($chat['name'] ?: ('Chat #' . $chat['id'])); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if (isUserAdmin()): ?>
                <a href="creerChat.php" class="btn btn-outline-primary mt-3 w-100">
                    <i class="fas fa-plus me-2"></i>Créer un chat
                </a>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <?php if ($chatId): ?>
                <div class="card">
                    <div class="card-header">
                        Participants :
                        <?php echo htmlspecialchars(implode(', ', array_map(function ($p) { return $p['username']; }, $participants))); ?>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($messages as $message): ?>
                            <div class="mb-3">
                                <strong><?php echo htmlspecialchars($message['username']); ?>:</strong>
                                <div><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($message['created_at']); ?></small>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                            <p class="text-muted">Aucun message pour le moment.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <form method="POST">
                            <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                            <div class="input-group">
                                <input type="text" name="message" class="form-control" placeholder="Votre message...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Sélectionnez un chat pour commencer.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
