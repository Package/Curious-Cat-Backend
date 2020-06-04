<?php


abstract class BaseService
{
    /**
     * @var PDO
     */
    protected $db;

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