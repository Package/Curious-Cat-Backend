<?php


class ProfileService extends BaseService
{
    /**
     * Returns stats for the provided user, number of answers, questions, etc.
     *
     * @param int $profileId
     * @return mixed
     */
    public function stats(int $profileId)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_profile_stats(:profile_info)");
        $statement->execute([$profileId]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the latest 50 questions this user has asked.
     *
     * @param int $profileId
     * @return array
     */
    public function questions(int $profileId)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_profile_questions(:profile_id)");
        $statement->execute([$profileId]);

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Returns the latest 50 answers this user has provided.
     *
     * @param int $profileId
     * @return array
     */
    public function answers(int $profileId)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_profile_answers(:profile_id)");
        $statement->execute([$profileId]);

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Fetches info about the user such as number of followers, etc.
     *
     * @param int $profileId
     * @param array $currentUser
     * @return mixed
     */
    public function info(int $profileId, array $currentUser)
    {
        $statement = $this->db->prepare("SELECT * FROM fn_profile_info(:profile_id, :current_user)");
        $statement->execute([$profileId, $currentUser["id"]]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}