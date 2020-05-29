<?php


class UserSearchResults extends SearchResults
{
    public static function search(string $query)
    {
        $db = Database::connect();

        $statement = $db->prepare("SELECT u.id, u.username, u.created_at FROM users u WHERE LOWER(username) LIKE :query LIMIT :limit");
        $statement->execute(['%' . strtolower($query) . '%', self::SEARCH_LIMIT]);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}
