<?php
require_once '../Config/connexion.php';

class ChatController {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(DNS, USER, PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function createChat($type, $name, array $participantIds) {
        $allowedTypes = ['direct', 'groupe'];
        if (!in_array($type, $allowedTypes, true)) {
            throw new Exception("Type de chat invalide.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO chats (type, name) VALUES (?, ?)");
        $stmt->execute([$type, $name]);
        $chatId = $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("INSERT INTO chat_participants (chat_id, user_id) VALUES (?, ?)");
        foreach (array_unique($participantIds) as $userId) {
            $stmt->execute([$chatId, $userId]);
        }

        return $chatId;
    }

    public function getChatsForUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT c.id, c.type, c.name, c.created_at
                                     FROM chats c
                                     INNER JOIN chat_participants cp ON cp.chat_id = c.id
                                     WHERE cp.user_id = ?
                                     ORDER BY c.created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChatSummariesForUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT c.id, c.type, c.name, c.created_at,
                                            lm.message AS last_message,
                                            lm.created_at AS last_message_at,
                                            COUNT(cp2.user_id) AS participant_count
                                     FROM chats c
                                     INNER JOIN chat_participants cp ON cp.chat_id = c.id AND cp.user_id = ?
                                     LEFT JOIN chat_participants cp2 ON cp2.chat_id = c.id
                                     LEFT JOIN chat_messages lm ON lm.id = (
                                         SELECT m.id FROM chat_messages m
                                         WHERE m.chat_id = c.id
                                         ORDER BY m.created_at DESC
                                         LIMIT 1
                                     )
                                     GROUP BY c.id
                                     ORDER BY COALESCE(lm.created_at, c.created_at) DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChatMessages($chat_id, $user_id, $limit = 50, $offset = 0) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM chat_participants WHERE chat_id = ? AND user_id = ?");
        $stmt->execute([$chat_id, $user_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Accès refusé à ce chat.");
        }

        $stmt = $this->pdo->prepare("SELECT m.id, m.message, m.created_at, u.username
                                     FROM chat_messages m
                                     INNER JOIN users u ON u.id = m.user_id
                                     WHERE m.chat_id = ?
                                     ORDER BY m.created_at DESC
                                     LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $chat_id, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getChatMessageCount($chat_id): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE chat_id = ?");
        $stmt->execute([$chat_id]);
        return (int) $stmt->fetchColumn();
    }

    public function addMessage($chat_id, $user_id, $message) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM chat_participants WHERE chat_id = ? AND user_id = ?");
        $stmt->execute([$chat_id, $user_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Accès refusé à ce chat.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO chat_messages (chat_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$chat_id, $user_id, $message]);
    }

    public function getChatParticipants($chat_id) {
        $stmt = $this->pdo->prepare("SELECT u.id, u.username, u.role
                                     FROM chat_participants cp
                                     INNER JOIN users u ON u.id = cp.user_id
                                     WHERE cp.chat_id = ?");
        $stmt->execute([$chat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
