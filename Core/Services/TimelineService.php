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
        $statement = $this->db->prepare("SELECT * FROM fn_home_timeline(:user_id)");
        $statement->execute([$user['id']]);

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}