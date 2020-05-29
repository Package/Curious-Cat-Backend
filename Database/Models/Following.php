<?php


class Following
{
    public $followed_user;
    public $following_user;
    public $created_at;

    /**
     * Follows a user.
     *
     * @param int $followedUser
     * @param array $followingUser
     * @throws OperationFailedException
     */
    public static function follow(int $followedUser, array $followingUser)
    {
        $db = Database::connect();

        if ($followingUser["id"] === $followedUser) {
            throw new OperationFailedException("You cannot follow yourself.");
        }

        if (!self::isFollowing($followedUser, $followingUser["id"])) {
            $statement = $db->prepare("INSERT INTO followers(followed_user, following_user, created_at) VALUES( :followed_user, :following_user, NOW())");
            $statement->execute([$followedUser, $followingUser["id"]]);

            if ($statement->rowCount() > 0) {
                Notification::create($followedUser, $followingUser["username"],Notification::NOTIFICATION_FOLLOWED_YOU);
            }
        }
    }

    /**
     * Un follows a user.
     *
     * @param int $followedUser
     * @param array $followingUser
     * @throws OperationFailedException
     */
    public static function unfollow(int $followedUser, array $followingUser)
    {
        $db = Database::connect();

        if ($followingUser["id"] === $followedUser) {
            throw new OperationFailedException("You cannot follow yourself.");
        }

        if (self::isFollowing($followedUser, $followingUser["id"])) {
            $statement = $db->prepare("DELETE FROM followers WHERE followed_user = :followed_user AND following_user = :following_user");
            $statement->execute([$followedUser, $followingUser["id"]]);
        }
    }

    /**
     * Returns all the users currently following the provided user.
     *
     * @param int $id
     * @param string $type
     * @return array
     * @throws OperationFailedException
     */
    public static function get(int $id, string $type)
    {
        $db = Database::connect();

        $sql = "SELECT 
                   u.id AS following_user,
                   u.username AS following_username,
                   f.created_at AS following_since
                FROM followers f
                    
                    INNER JOIN users u 
                        ON u.id = f.following_user";

        switch ($type) {
            case 'followers':
                $sql .= " WHERE f.followed_user = :user_id";
                break;
            case "following":
                $sql .= " WHERE f.following_user = :user_id";
                break;
            default:
                throw new OperationFailedException("Type must be one of [followers, following].");
        }
        
        $statement = $db->prepare($sql);
        $statement->execute([$id]);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Is the $followedUser being followed by the $followingUser?
     *
     * @param int $followedUser
     * @param int $followingUser
     * @return bool
     */
    private static function isFollowing(int $followedUser, int $followingUser) : bool
    {
        $db = Database::connect();

        $statement = $db->prepare("SELECT * FROM followers WHERE followed_user = :followed_user AND following_user = :following_user");
        $statement->execute([$followedUser, $followingUser]);

        return $statement->rowCount() > 0;
    }
}