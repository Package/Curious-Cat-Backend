<?php


class UserSearchService extends SearchService
{

    /**
     * Performs a search for users.
     *
     * @param string $query
     * @return array
     */
    public function search(string $query)
    {
        $statement = $this->db->prepare("SELECT u.id, u.username, u.created_at FROM users u WHERE LOWER(username) LIKE :query LIMIT :limit");
        $statement->execute(['%' . strtolower($query) . '%', self::SEARCH_LIMIT]);

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}