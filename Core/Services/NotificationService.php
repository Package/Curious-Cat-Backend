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
     * @param int|null $contextId
     * @param int $hidden
     * @throws OperationFailedException
     */
    public function create(int $toUser, array $fromUser, int $messageType, string $context = null, int $contextId = null, int $hidden = 0): void
    {
        $formattedMessage = $this->formatNotification($fromUser["username"], $messageType);
        if (!$formattedMessage) {
            throw new OperationFailedException("Could not send notification.");
        }

        $statement = $this->db->prepare("INSERT INTO notifications(label, notification_read, user_id, created_at, from_user, notification_type, context, context_id, hidden) 
                                             VALUES(:label, 0, :user, NOW(), :from_user, :notification_type, :context, :context_id, :hidden)");
        $statement->execute([$formattedMessage, $toUser, $fromUser["id"], $messageType, $context, $contextId, $hidden]);
    }

    /**
     * Marks the provided user's notifications as read.
     *
     * @param int $userId
     * @param int $notification
     * @throws OperationFailedException
     */
    public function read(int $userId, int $notification): void
    {
        if (!$notification || $notification <= 0) {
            throw new OperationFailedException("Please provide notification ID in request.");
        }

        $statement = $this->db->prepare("UPDATE notifications SET notification_read = 1 WHERE user_id = :user AND id = :notification");
        $statement->execute([$userId, $notification]);
    }

    /**
     * Returns all unread notifications for the provided user (up to a max of 50).
     *
     * @param int $userId
     * @return array
     */
    public function get(int $userId)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_notification_get(:user_id)");
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