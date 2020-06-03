<?php


class StatsService extends BaseService
{

    /**
     * Returns the 5 newest users.
     * @return array
     */
    public function newest()
    {
        $statement = $this->db->query("SELECT * FROM fn_stats_newest()");
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
        $statement = $this->db->query("SELECT * FROM fn_stats_questions()");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Returns the top 5 users who have answered the most questions
     * @return array
     */
    public function topAnswers()
    {
        $statement = $this->db->query("SELECT * FROM fn_stats_answers()");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}