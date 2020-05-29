<?php

class Notification
{
    public $id;
    public $label;
    public $notification_read;
    public $user_id;
    public $created_at;

    public static function create()
    {
        throw new Exception("Not Implemented");
    }

    public static function read()
    {
        throw new Exception("Not Implemented");
    }
}