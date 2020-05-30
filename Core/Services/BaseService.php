<?php


abstract class BaseService
{
    /**
     * @var PDO
     */
    protected $db;

    protected static $questionService;
    protected static $answerService;
    protected static $profileService;
    protected static $notificationService;
    protected static $userService;
    protected static $followService;
    protected static $searchService;

    /**
     * BaseService constructor.
     */
    public function __construct()
    {
        if ($this->db == null) {
            $this->db = (new Database())->connect();
        }
    }
}