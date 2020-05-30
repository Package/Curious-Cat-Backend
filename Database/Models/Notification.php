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

    public static $notificationMap = [
        self::NOTIFICATION_ASKED_QUESTION => "asked you a question.",
        self::NOTIFICATION_ANSWERED_QUESTION => "answered to your question.",
        self::NOTIFICATION_FOLLOWED_YOU => "followed you.",
        self::NOTIFICATION_TEST_NOTIFICATION => "This is just a test notification.",
    ];
}