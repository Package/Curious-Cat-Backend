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
        $statement = $this->db->prepare("
            SELECT 
                q.id AS question_id, 
                q.label AS question_label,
                q.created_at AS question_timestamp, 
                q.name_hidden AS question_name_hidden,
                CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.id END AS from_user,
                CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.username END AS from_user_name,
                tu.id AS target_user,
                tu.username AS target_user_name,
                a.id AS answer_id,
                a.label AS answer_label,
                a.created_at AS answer_timestamp
            
            FROM questions q
                
                INNER JOIN users fu
                    ON fu.id = q.from_user
                
                INNER JOIN users tu
                    ON tu.id = q.target_user
            
                LEFT OUTER JOIN answers a 
                    ON q.id = a.question_id 
            
            WHERE 
                  LOWER(q.label) LIKE :query OR 
                  LOWER(a.label) LIKE :query
            
            ORDER BY
                  q.created_at DESC
            
            LIMIT :limit
            ");

        $statement->execute(['%' . strtolower($query) . '%', self::SEARCH_LIMIT]);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}