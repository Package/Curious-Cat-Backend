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
            $config = parse_ini_file('../config/development.ini');
            if (!$config) {
                throw new Exception("Unable to read config file.");
            }

            $connectionString = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']};user={$config['user']};password={$config['password']}";
            self::$_connection = new PDO($connectionString);
            self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return self::$_connection;
        } catch (Exception $e) {
            exit("Error with connecting to database: {$e->getMessage()}");
        }
    }
}