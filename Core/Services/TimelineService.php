<?php

class TimelineService extends BaseService
{
    /**
     * Returns timeline information for the provided user.
     * e.g. answers from people they're following
     * @param array $user
     * @return array
     */
    public function timeline(array $user)
    {
        $statement = $this->db->prepare("
            SELECT 
                   q.id AS question_id,
                   q.label AS question_label,
                   q.created_at AS question_timestamp,
                   q.name_hidden AS question_name_hidden,
                   a.id AS answer_id,
                   a.label AS answer_label,
                   a.created_at AS answer_timestamp,
                   u2.id AS target_user,
                   u2.username AS target_user_name,
                   CASE WHEN q.name_hidden = 1 THEN NULL ELSE u1.id END AS from_user,
                   CASE WHEN q.name_hidden = 1 THEN NULL ELSE u1.username END AS from_user_name
            
            FROM questions q

                INNER JOIN answers a 
                    ON q.id = a.question_id
                
                INNER JOIN users u1 -- From
                    ON u1.id = q.from_user
                
                INNER JOIN users u2 -- To
                    ON u2.id = q.target_user
                
                INNER JOIN followers f
                    ON f.following_user = :following_user
                    AND f.followed_user = u2.id
            
            ORDER BY a.created_at DESC
            LIMIT 50
        ");
        $statement->execute([$user['id']]);

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}