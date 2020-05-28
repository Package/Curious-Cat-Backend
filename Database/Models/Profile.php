<?php


class Profile
{

    /**
     * Returns stats for the provided user, number of answers, questions, etc.
     *
     * @param int $profileId
     * @return mixed
     */
    public static function stats(int $profileId)
    {
        $db = Database::connect();

        $statement = $db->prepare("
            SELECT 
                   COUNT(fu.id) AS questions_asked,
                   COUNT(tu.id) AS questions_answered
            FROM
                questions q
            
                LEFT OUTER JOIN users tu
                    ON tu.id = q.target_user
                    
                LEFT OUTER JOIN users fu
                    ON fu.id = q.from_user
                    AND q.name_hidden = 0 -- Don't count questions hidden in the total
            
            WHERE
                tu.id = :profile_id OR fu.id = :profile_id
        ");

        $statement->bindParam(":profile_id",$profileId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the profile timeline info for a user
     *
     * @param int $profileId
     * @param array $currentUser
     * @return array
     */
    public static function timeline(int $profileId, array $currentUser)
    {
        $db = Database::connect();

        $statement = $db->prepare("
            SELECT 
                   q.id AS question_id,
                   q.label AS question_label,
                   q.created_at AS question_timestamp,
                   q.name_hidden AS question_name_hidden,
                   a.id AS answer_id,
                   a.label AS answer_label,
                   a.created_at AS answer_timestamp,
                   tu.id AS target_user,
                   tu.username AS target_user_name,
                   fu.id AS from_user,
                   fu.username AS from_user_name
            
            FROM 
                 questions q
                     
                LEFT OUTER JOIN answers a
                    ON q.id = a.question_id
                INNER JOIN users tu
                    ON tu.id = q.target_user
                INNER JOIN users fu
                    ON fu.id = q.from_user
            
            WHERE
                q.target_user = :profile_id OR q.from_user = :profile_id
            
            ORDER BY 
                q.created_at DESC
        ");

        $statement->bindParam(":profile_id",$profileId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Fetches info about the user such as number of followers, etc.
     *
     * @param int $profileId
     * @param array $currentUser
     * @return mixed
     */
    public static function info(int $profileId, array $currentUser)
    {
        $db = Database::connect();

        // Todo: update when followers is a thing
        $statement = $db->prepare("
            SELECT 
                   0 AS follower_count,
                   0 AS following_count,
                   false AS is_following,
                   CASE WHEN u.id = :current_user THEN TRUE ELSE FALSE END AS own_profile
            FROM
                users u
            
            WHERE
                u.id = :profile_id
        ");

        $statement->bindParam(":profile_id",$profileId, PDO::PARAM_INT);
        $statement->bindParam(":current_user",$currentUser['id'], PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}