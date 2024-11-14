<?php

class DBConnection
{

    private static $conn = null;

    public static function get_connection()
    {
        $host = "localhost";
        $user = "root";
        $password = "";
        $database = "finance";
        if (self::$conn == null) {
            self::$conn = new mysqli($host, $user, $password, $database);
        }
        if (self::$conn->connect_error) {
            die("you have database error" . self::$conn->connect_error);
        }
        return self::$conn;
    }

    public static function close_connection()
    {
        if (self::$conn != null) {
            self::$conn->close();
        }
    }
}
