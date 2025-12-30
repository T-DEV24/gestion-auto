<?php
require_once '../Config/auth.php';
requireLogin();

require_once '../Controller/ChatController.php';
$controller = new ChatController();
$error = null;
$success = $_GET['success'] ?? null;

$chatId = isset($_GET['chat_id']) ? (int) $_GET['chat_id'] : null;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = 30;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new Exception("Jeton CSRF invalide.");
        }
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

$chatSummaries = $controller->getChatSummariesForUser($_SESSION['user_id']);
$totalMessages = $chatId ? $controller->getChatMessageCount($chatId) : 0;
$totalPages = $chatId ? (int) ceil($totalMessages / $limit) : 0;
$page = $totalPages > 0 ? min($page, $totalPages) : 1;
$offset = ($page - 1) * $limit;
$messages = $chatId ? $controller->getChatMessages($chatId, $_SESSION['user_id'], $limit, $offset) : [];
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
                <?php foreach ($chatSummaries as $chat): ?>
                    <a href="chat.php?chat_id=<?php echo $chat['id']; ?>" class="list-group-item list-group-item-action<?php echo $chatId === (int) $chat['id'] ? ' active' : ''; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><?php echo htmlspecialchars($chat['name'] ?: ('Chat #' . $chat['id'])); ?></strong>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($chat['type']); ?></span>
                        </div>
                        <div class="small text-muted">
                            <?php if (!empty($chat['last_message'])): ?>
                                <?php echo htmlspecialchars(mb_strimwidth($chat['last_message'], 0, 60, '...')); ?>
                            <?php else: ?>
                                Aucun message
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted d-flex justify-content-between">
                            <span><?php echo htmlspecialchars($chat['participant_count']); ?> participant(s)</span>
                            <span><?php echo htmlspecialchars($chat['last_message_at'] ?? $chat['created_at']); ?></span>
                        </div>
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
                    <?php if ($totalPages > 1): ?>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <a class="btn btn-outline-secondary btn-sm<?php echo $page <= 1 ? ' disabled' : ''; ?>" href="chat.php?chat_id=<?php echo $chatId; ?>&page=<?php echo max(1, $page - 1); ?>">Précédent</a>
                            <span class="small text-muted">Page <?php echo $page; ?> / <?php echo $totalPages; ?></span>
                            <a class="btn btn-outline-secondary btn-sm<?php echo $page >= $totalPages ? ' disabled' : ''; ?>" href="chat.php?chat_id=<?php echo $chatId; ?>&page=<?php echo min($totalPages, $page + 1); ?>">Suivant</a>
                        </div>
                    <?php endif; ?>
                    <div class="card-footer">
                        <form method="POST">
                            <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(getCsrfToken()); ?>">
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
