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
        $statement = $this->db->prepare("SELECT * FROM fn_question_get(:question_id)");
        $statement->execute([$questionNumber]);

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
        $statement->execute([$id, $user["id"]]);

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

        $statement = $this->db->prepare("INSERT INTO questions (label, target_user, from_user, created_at, name_hidden) VALUES (:label, :target_user, :from_user, NOW(), :name_hidden)");
        $statement->execute([$label, $targetUser, $user["id"], $nameHidden]);

        $successfullyCreated = $statement->rowCount() > 0;
        if ($successfullyCreated) {
            $notificationService = new NotificationService;
            $notificationService->create($targetUser, $user,Notification::NOTIFICATION_ASKED_QUESTION, $label, $this->db->lastInsertId(), $nameHidden);
        }

        return $successfullyCreated;
    }
}