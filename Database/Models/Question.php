<?php

class Question
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
     * @var int
     */
    public $target_user;

    /**
     * @var int
     */
    public $from_user;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var bool
     */
    public $name_hidden;
}