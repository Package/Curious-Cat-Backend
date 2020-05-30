<?php


abstract class SearchService extends BaseService
{
    protected const SEARCH_LIMIT = 50;

    /**
     * SearchService constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public abstract function search(string $query);
}