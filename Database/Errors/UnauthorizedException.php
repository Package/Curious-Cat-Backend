<?php

class UnauthorizedException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}