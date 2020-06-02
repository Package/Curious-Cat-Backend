<?php


class QuestionService extends BaseService
{
    /**
     * Returns details of all questions, (or a specific provided question)
     *
     * @param int $questionNumber
     * @return array
     */
    public function get(int $questionNumber = 0) {
        $statement = $this->db->prepare("
            SELECT 
                   q.id,
                   q.created_at,
                   q.name_hidden,
                   q.label,
                   q.target_user,
                   CASE WHEN q.name_hidden = 1 THEN NULL ELSE u.username END AS from_username,
                   CASE WHEN q.name_hidden = 1 THEN NULL ELSE u.id END AS from_user
                    
            FROM questions q
            
                INNER JOIN users u 
                    ON u.id = q.from_user
            
            WHERE q.id = :question_number OR :question_number = 0
        ");
        $statement->bindParam(":question_number", $questionNumber, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Question::class);
    }

    /**
     * Handles deleting a question.
     *
     * @param int $id
     * @param array $user
     * @return bool
     * @throws OperationFailedException
     */
    public function delete(int $id, array $user) : bool
    {
        if (!$id || !$user['id']) {
            throw new OperationFailedException("Question does not exist or you are not authenticated.");
        }

        $statement = $this->db->prepare("DELETE FROM questions WHERE id = :question_number AND from_user = :user_id");
        $statement->bindParam(":question_number", $id, PDO::PARAM_INT);
        $statement->bindParam(":user_id", $user['id'], PDO::PARAM_INT);
        $statement->execute();

        $wasDeleted = $statement->rowCount() > 0;
        if (!$wasDeleted) {
            throw new OperationFailedException("Either you do not have permission to do that or the question does not exist.");
        }

        return $wasDeleted;
    }

    /**
     * Creates a new question.
     *
     * @param string $label
     * @param int $targetUser
     * @param array $user
     * @param int $nameHidden
     * @return bool
     * @throws OperationFailedException
     */
    public function create(string $label, int $targetUser, array $user, int $nameHidden)
    {
        if (!$label || strlen($label) == 0 || !$targetUser || !$user['id']) {
            throw new OperationFailedException("An error occurred. Please login and try again.");
        }

        $statement = $this->db->prepare("INSERT INTO questions (label, target_user, from_user, created_at, name_hidden)
                                            VALUES (:label, :target_user, :from_user, NOW(), :name_hidden)");
        $statement->bindParam(":label", $label, PDO::PARAM_STR);
        $statement->bindParam(":target_user", $targetUser, PDO::PARAM_INT);
        $statement->bindParam(":from_user", $user['id'], PDO::PARAM_INT);
        $statement->bindParam(":name_hidden", $nameHidden, PDO::PARAM_INT);
        $statement->execute();

        $successfullyCreated = $statement->rowCount() > 0;
        if ($successfullyCreated) {
            $notificationService = new NotificationService;
            $notificationService->create($targetUser, $user,Notification::NOTIFICATION_ASKED_QUESTION, $label, $this->db->lastInsertId(), $nameHidden);
        }

        return $successfullyCreated;
    }
}