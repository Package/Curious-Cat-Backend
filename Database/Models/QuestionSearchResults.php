<?php


class QuestionSearchResults extends SearchResults
{
    /**
     * Performs a search on Questions and Answers for the provided search query.
     *
     * Limited number of results are returned, ordered by newest first.
     *
     * @param string $query
     * @return array
     */
    public static function search(string $query)
    {
        $db = Database::connect();

        $statement = $db->prepare("
            SELECT 
                q.id AS question_id, 
                q.created_at AS question_created_at, 
                q.label AS question_label,
                q.name_hidden AS question_name_hidden,
                CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.id END AS from_userid,
                CASE WHEN q.name_hidden = 1 THEN NULL ELSE fu.username END AS from_username,
                tu.id AS to_userid,
                tu.username AS to_username,
                COUNT(a.id) AS num_answers
            
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
                        
            GROUP BY
                q.id, 
                q.created_at, 
                q.label, 
                fu.username,
                fu.id,
                tu.username,
                tu.id
            
            ORDER BY
                  q.created_at DESC
            
            LIMIT :limit
            ");

        $statement->execute(['%' . strtolower($query) . '%', self::SEARCH_LIMIT]);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}