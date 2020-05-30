<?php

class Notification
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $label;

    /**
     * @var bool
     */
    public $notification_read;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var string
     */
    public $created_at;

    public const NOTIFICATION_ASKED_QUESTION = 1;
    public const NOTIFICATION_ANSWERED_QUESTION = 2;
    public const NOTIFICATION_FOLLOWED_YOU = 3;

    public static $notificationMap = [
        self::NOTIFICATION_ASKED_QUESTION => "asked you a question.",
        self::NOTIFICATION_ANSWERED_QUESTION => "answered to your question.",
        self::NOTIFICATION_FOLLOWED_YOU => "followed you.",
    ];
}