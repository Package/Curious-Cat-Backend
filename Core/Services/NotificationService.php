<?php


class NotificationService extends BaseService
{

    /**
    * Creates a new notification to the provided user.
    *
    * @param string $triggeredBy
    * @param int $toUser
    * @param int $messageType
    * @throws OperationFailedException
    */
    public function create(int $toUser, string $triggeredBy, int $messageType): void
    {
        $formattedMessage = $this->formatNotification($triggeredBy, $messageType);
        if (!$formattedMessage) {
            throw new OperationFailedException("Could not send notification.");
        }

        $statement = $this->db->prepare("INSERT INTO notifications( label, notification_read, user_id, created_at) 
                                             VALUES(:label, 0, :user, NOW())");
        $statement->execute([$formattedMessage, $toUser]);
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
        $statement = $this->db->prepare("SELECT * FROM notifications WHERE user_id = :user AND notification_read = 0 ORDER BY created_at DESC LIMIT 50");
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