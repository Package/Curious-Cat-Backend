<?php

class Question
{
    public $id;
    public $label;
    public $target_user;
    public $from_user;
    public $created_at;
    public $name_hidden;

    /**
     * Returns details of all questions, (or a specific provided question)
     *
     * @param int $questionNumber
     * @return array
     */
    public static function get(int $questionNumber = 0) {
        $db = Database::connect();

        $statement = $db->prepare("SELECT * FROM questions WHERE id = :question_number OR :question_number = 0");
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
    public static function delete(int $id, array $user) : bool
    {
        if (!$id || !$user['id']) {
            throw new OperationFailedException("Question does not exist or you are not authenticated.");
        }

        $db = Database::connect();

        $statement = $db->prepare("DELETE FROM questions WHERE id = :question_number AND from_user = :user_id");
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
    public static function create(string $label, int $targetUser, array $user, int $nameHidden)
    {
        if (!$label || strlen($label) == 0 || !$targetUser || !$user['id']) {
            throw new OperationFailedException("An error occurred. Please login and try again.");
        }

        $db = Database::connect();

        $statement = $db->prepare("INSERT INTO questions (label, target_user, from_user, created_at, name_hidden)
                                            VALUES (:label, :target_user, :from_user, NOW(), :name_hidden)");
        $statement->bindParam(":label", $label, PDO::PARAM_STR);
        $statement->bindParam(":target_user", $targetUser, PDO::PARAM_INT);
        $statement->bindParam(":from_user", $user['id'], PDO::PARAM_INT);
        $statement->bindParam(":name_hidden", $nameHidden, PDO::PARAM_INT);
        $statement->execute();

        return true;
    }
}