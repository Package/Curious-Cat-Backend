<?php


class NotificationService extends BaseService
{

    /**
     * Creates a new notification to the provided user.
     *
     * @param int $toUser
     * @param array $fromUser
     * @param int $messageType
     * @param string|null $context
     * @throws OperationFailedException
     */
    public function create(int $toUser, array $fromUser, int $messageType, string $context = null, int $contextId = null): void
    {
        $formattedMessage = $this->formatNotification($fromUser["username"], $messageType);
        if (!$formattedMessage) {
            throw new OperationFailedException("Could not send notification.");
        }

        $statement = $this->db->prepare("INSERT INTO notifications(label, notification_read, user_id, created_at, from_user, notification_type, context, context_id) 
                                             VALUES(:label, 0, :user, NOW(), :from_user, :notification_type, :context, :context_id)");
        $statement->execute([$formattedMessage, $toUser, $fromUser["id"], $messageType, $context, $contextId]);
    }

    /**
     * Marks the provided user's notifications as read.
     *
     * @param $userId
     */
    public function read(int $userId): void
    {
        $statement = $this->db->prepare("UPDATE notifications SET notification_read = 1 WHERE user_id = :user");
        $statement->execute([$userId]);
    }

    /**
     * Returns all unread notifications for the provided user (up to a max of 50).
     *
     * @param int $userId
     * @return array
     */
    public function get(int $userId)
    {
        $statement = $this->db->prepare("
                SELECT n.*, 
                       nt.type AS notification_type_string,
                       u.username AS from_username
                FROM notifications n
                    INNER JOIN users u
                        ON u.id = n.from_user
                    INNER JOIN notification_type nt
                        ON nt.id = n.notification_type
                WHERE user_id = :user AND notification_read = 0 
                ORDER BY created_at DESC 
                LIMIT 50");
        $statement->execute([$userId]);

        return $statement->fetchAll(PDO::FETCH_CLASS, Notification::class);
    }

    /**
     * Formats the notification string.
     * e.g. John Doe asked you a question.
     *
     * @param string $triggeredBy
     * @param int $notificationType
     * @return string|bool
     */
    private function formatNotification(string $triggeredBy, int $notificationType)
    {
        if (!array_key_exists($notificationType, Notification::$notificationMap)) {
            return false;
        }

        if ($triggeredBy && strlen($triggeredBy) > 0) {
            return sprintf("%s %s", $triggeredBy, Notification::$notificationMap[$notificationType]);
        } else {
            return Notification::$notificationMap[$notificationType];
        }
    }
}