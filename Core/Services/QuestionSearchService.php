<?php


class QuestionSearchService extends SearchService
{

    /**
     * Performs a search on Questions and Answers for the provided search query.
     *
     * Limited number of results are returned, ordered by newest first.
     *
     * @param string $query
     * @return array
     */
    public function search(string $query)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_search_question(:query, :limit)");
        $statement->execute(['%' . strtolower($query) . '%', self::SEARCH_LIMIT]);

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}