<?php


class AnswerService extends BaseService
{
    /**
     * Returns the answers to the question provided.
     * @param int $question
     * @return mixed
     */
    public function forQuestion(int $question)
    {
        $statement = $this->db->prepare("SELECT * FROM answers WHERE question_id = :question");
        $statement->bindValue(':question', $question, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Answer::class);
    }

    /**
     * Creates a new answer for a question.
     *
     * @param int $questionID
     * @param array $user
     * @param string $label
     * @return bool
     * @throws OperationFailedException
     * @throws UnauthorizedException
     */
    public function create(int $questionID, array $user, string $label)
    {
        // Validate input
        if (!$questionID || !$label || strlen($label) == 0 || !$user['id']) {
            throw new OperationFailedException("An error occurred. Please login and try again.");
        }

        $questionService = new QuestionService;
        $question = $questionService->get($questionID);
        if (!$question || count($question) == 0 || $question[0]->target_user != $user['id']) {
            throw new UnauthorizedException("You do not have permission to post an answer to this question.");
        }

        // Create the new answer
        $statement = $this->db->prepare("INSERT INTO answers (question_id, label, created_at, user_id)
                                            VALUES(:question_id, :label, NOW(), :user_id)");
        $statement->bindParam(":question_id", $questionID, PDO::PARAM_INT);
        $statement->bindParam(":label", $label, PDO::PARAM_STR);
        $statement->bindParam(":user_id", $user['id'], PDO::PARAM_INT);
        $statement->execute();

        $successfullyAnswered = $statement->rowCount() > 0;
        if ($successfullyAnswered) {
            // Send a notification that the question has been answered
            $notificationService = new NotificationService;
            $notificationService->create($question[0]->from_user, $user, Notification::NOTIFICATION_ANSWERED_QUESTION, $label, $this->db->lastInsertId());
        }

        return $successfullyAnswered;
    }

    /**
     * Deletes a question.
     *
     * @param int $answerID
     * @param array $user
     * @return bool
     * @throws OperationFailedException|UnauthorizedException
     */
    public function delete(int $answerID, array $user)
    {
        // Validate input
        if (!$answerID || !$user['id']) {
            throw new OperationFailedException("An error occurred. Please login and try again.");
        }

        $statement = $this->db->prepare("DELETE FROM answers WHERE id = :answer_id AND user_id = :user_id");
        $statement->bindParam(":answer_id", $answerID, PDO::PARAM_INT);
        $statement->bindParam(":user_id", $user['id'], PDO::PARAM_INT);
        $statement->execute();

        // Only the user who created it can delete
        if ($statement->rowCount() < 1) {
            throw new UnauthorizedException("You do not have permission to delete that answer or it does not exist.");
        }

        return true;
    }
}