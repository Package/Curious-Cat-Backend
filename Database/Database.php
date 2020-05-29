<?php

class Database
{
    /**
     * The connection to the database.
     * @var PDO|null
     */
    private static $_connection = null;

    /**
     * Gets the connection to the database.
     *
     * @return PDO
     */
    public static function connect() : PDO
    {
        if (self::$_connection != null) {
            return self::$_connection;
        }

        try {
            $connectionString = "pgsql:host={$_ENV['host']};port={$_ENV['port']};dbname={$_ENV['database']};user={$_ENV['user']};password={$_ENV['password']}";
            self::$_connection = new PDO($connectionString);
            self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return self::$_connection;
        } catch (Exception $e) {
            exitWithMessage("Error with connecting to database: {$e->getMessage()}", 500);
        }
    }
}