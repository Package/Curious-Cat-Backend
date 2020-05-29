<?php

abstract class SearchResults
{
    protected const SEARCH_LIMIT = 50;

    public static abstract function search(string $query);
}

