<?php

class Notification
{
    public $id;
    public $label;
    public $notification_read;
    public $user_id;
    public $created_at;

    public const NOTIFICATION_ASKED_QUESTION = 1;
    public const NOTIFICATION_ANSWERED_QUESTION = 2;
    public const NOTIFICATION_FOLLOWED_YOU = 3;
    public const NOTIFICATION_TEST_NOTIFICATION = 999;

    private static $notificationMap = [
        self::NOTIFICATION_ASKED_QUESTION => "asked you a question.",
        self::NOTIFICATION_ANSWERED_QUESTION => "answered to your question.",
        self::NOTIFICATION_FOLLOWED_YOU => "followed you.",
        self::NOTIFICATION_TEST_NOTIFICATION => "This is just a test notification.",
    ];

    /**
     * Creates a new notification to the provided user.
     *
     * @param string $triggeredBy
     * @param int $toUser
     * @param int $messageType
     * @throws OperationFailedException
     */
    public static function create(int $toUser, string $triggeredBy, int $messageType): void
    {
        $db = Database::connect();

        $formattedMessage = self::formatNotification($triggeredBy, $messageType);
        if (!$formattedMessage) {
            throw new OperationFailedException("Could not send notification.");
        }

        $statement = $db->prepare("INSERT INTO notifications( label, notification_read, user_id, created_at) 
                                             VALUES(:label, 0, :user, NOW())");
        $statement->execute([$formattedMessage, $toUser]);
    }

    /**
     * Marks the provided user's notifications as read.
     *
     * @param $userId
     */
    public static function read(int $userId): void
    {
        $db = Database::connect();

        $statement = $db->prepare("UPDATE notifications SET notification_read = 1 WHERE user_id = :user");
        $statement->execute([$userId]);
    }

    /**
     * Returns all unread notifications for the provided user (up to a max of 50).
     *
     * @param int $userId
     * @return array
     */
    public static function get(int $userId)
    {
        $db = Database::connect();

        $statement = $db->prepare("SELECT * FROM notifications WHERE user_id = :user AND notification_read = 0 ORDER BY created_at DESC LIMIT 50");
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
    private static function formatNotification(string $triggeredBy, int $notificationType)
    {
        if (!array_key_exists($notificationType, self::$notificationMap)) {
            return false;
        }

        if ($triggeredBy && strlen($triggeredBy) > 0) {
            return sprintf("%s %s", $triggeredBy, self::$notificationMap[$notificationType]);
        } else {
            return self::$notificationMap[$notificationType];
        }
    }
}