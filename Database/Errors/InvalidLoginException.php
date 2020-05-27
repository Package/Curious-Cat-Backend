<?php


class InvalidLoginException extends Exception
{
    /**
     * InvalidLoginException constructor.
     * @param $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}