<?php

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}


    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {

                $host = defined('DB_HOST') ? DB_HOST : 'localhost';
                $user = defined('DB_USER') ? DB_USER : 'root';
                $pass = defined('DB_PASS') ? DB_PASS : 'Sa@123456';
                $name = defined('DB_NAME') ? DB_NAME : 'coachPro';

                $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new Exception("Could not connect to the database.");
            }
        }

        return self::$instance;
    }
}
