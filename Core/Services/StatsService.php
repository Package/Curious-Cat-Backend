<?php


class StatsService extends BaseService
{

    /**
     * Returns the 5 newest users.
     * @return array
     */
    public function newest()
    {
        $statement = $this->db->query("
            SELECT  
                   u.id,
                   u.username,
                   u.created_at AS counter
            
            FROM users u 
            ORDER BY u.created_at DESC LIMIT 5");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Returns the top 5 users with the most questions asked.
     *
     * @return array
     */
    public function topQuestions()
    {
        $statement = $this->db->query("
            SELECT  
                   u.id,
                   u.username,
                   u.created_at,
                   COUNT(DISTINCT q.id) AS counter 
            FROM users u 
            
                INNER JOIN questions q
                    ON q.from_user = u.id
                    AND q.name_hidden = 0
            
            GROUP BY 
                u.id, u.username, u.created_at
            
            ORDER BY counter DESC LIMIT 5");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Returns the top 5 users who have answered the most questions
     * @return array
     */
    public function topAnswers()
    {
        $statement = $this->db->query("
            SELECT  
                   u.id,
                   u.username,
                   u.created_at,
                   COUNT(DISTINCT a.id) AS counter 
            FROM users u 
                
                INNER JOIN answers a
                    ON a.user_id = u.id
            
            GROUP BY 
                u.id, u.username, u.created_at            
            
            ORDER BY counter DESC LIMIT 5");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}