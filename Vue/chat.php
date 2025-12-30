<?php
require_once '../Config/auth.php';
requireLogin();

require_once '../Controller/ChatController.php';
require_once '../Controller/UserController.php';
$controller = new ChatController();
$userController = new UserController();
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
        if (isset($_POST['action']) && isUserAdmin()) {
            $manageChatId = (int) $_POST['chat_id'];
            $targetUserId = (int) ($_POST['user_id'] ?? 0);
            if ($_POST['action'] === 'add_participant') {
                $controller->addParticipant($manageChatId, $targetUserId);
                header('Location: chat.php?chat_id=' . $manageChatId . '&success=Participant ajouté');
                exit();
            }
            if ($_POST['action'] === 'remove_participant') {
                $controller->removeParticipant($manageChatId, $targetUserId);
                header('Location: chat.php?chat_id=' . $manageChatId . '&success=Participant supprimé');
                exit();
            }
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

$chatSummaries = isUserAdmin()
    ? $controller->getAllChatSummaries()
    : $controller->getChatSummariesForUser($_SESSION['user_id']);
$totalMessages = $chatId ? $controller->getChatMessageCount($chatId) : 0;
$totalPages = $chatId ? (int) ceil($totalMessages / $limit) : 0;
$page = $totalPages > 0 ? min($page, $totalPages) : 1;
$offset = ($page - 1) * $limit;
$messages = [];
$participants = $chatId ? $controller->getChatParticipants($chatId) : [];
$canReply = false;
if ($chatId) {
    try {
        $messages = $controller->getChatMessages($chatId, $_SESSION['user_id'], $limit, $offset);
        $canReply = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
$allUsers = isUserAdmin() ? $userController->getAllUsers() : [];

ob_start();
?>

<style>
    .chat-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eef0f5;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    .chat-list .list-group-item {
        border: 0;
        border-bottom: 1px solid #f0f2f7;
        padding: 12px 16px;
    }
    .chat-list .list-group-item.active {
        background: rgba(107, 101, 234, 0.12);
        color: #2b2b2b;
        border-left: 4px solid #6b65ea;
    }
    .chat-message {
        padding: 10px 14px;
        border-radius: 12px;
        max-width: 85%;
        margin-bottom: 12px;
        background: #f5f6fb;
    }
    .chat-message .meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 6px;
    }
    .chat-message.me {
        margin-left: auto;
        background: rgba(107, 101, 234, 0.12);
        border: 1px solid rgba(107, 101, 234, 0.3);
    }
    .chat-header {
        background: #f8f9fb;
        border-bottom: 1px solid #eef0f5;
    }
    .participant-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        background: #f1f3f9;
        font-size: 0.85rem;
        margin: 4px 6px 0 0;
    }
    .chat-tools {
        background: #fafbff;
        border: 1px dashed #dfe3f0;
        border-radius: 10px;
        padding: 12px;
    }
</style>

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
            <div class="list-group chat-list chat-container">
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
                <div class="card chat-container">
                    <div class="card-header chat-header">
                        <strong>Participants</strong>
                        <div class="mt-2">
                            <?php foreach ($participants as $participant): ?>
                                <span class="participant-chip">
                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($participant['username']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($messages as $message): ?>
                            <div class="chat-message<?php echo $message['username'] === ($_SESSION['username'] ?? '') ? ' me' : ''; ?>">
                                <strong><?php echo htmlspecialchars($message['username']); ?></strong>
                                <div><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                <div class="meta"><?php echo htmlspecialchars($message['created_at']); ?></div>
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
                        <?php if ($canReply): ?>
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
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">Vous n'êtes pas membre de ce chat. Ajoutez-vous avant de répondre.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isUserAdmin()): ?>
                    <div class="chat-tools mt-3">
                        <h5 class="mb-3"><i class="fas fa-user-cog me-2"></i>Gérer les participants</h5>
                        <form method="POST" class="row g-2 align-items-end">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(getCsrfToken()); ?>">
                            <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                            <input type="hidden" name="action" value="add_participant">
                            <div class="col-md-8">
                                <label class="form-label">Ajouter un utilisateur</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($allUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>">
                                            <?php echo htmlspecialchars($user['username'] . ' (' . $user['role'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-primary w-100" type="submit">Ajouter</button>
                            </div>
                        </form>

                        <div class="mt-3">
                            <label class="form-label">Participants actuels</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($participants as $participant): ?>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(getCsrfToken()); ?>">
                                        <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                                        <input type="hidden" name="action" value="remove_participant">
                                        <input type="hidden" name="user_id" value="<?php echo $participant['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-user-minus me-1"></i><?php echo htmlspecialchars($participant['username']); ?>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
