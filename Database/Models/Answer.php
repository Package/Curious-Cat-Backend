<?php

class Answer
{
    public $id;
    public $question_id;
    public $label;
    public $created_at;

    /**
     * Returns the answers to the question provided.
     * @param int $question
     * @return mixed
     */
    public static function forQuestion(int $question)
    {
        $db = Database::connect();
        $statement = $db->prepare("SELECT * FROM answers WHERE question_id = :question");
        $statement->bindValue(':question', $question, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Answer::class);
    }
}