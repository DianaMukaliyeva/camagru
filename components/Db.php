<?php
final class Db {

    // The default driver settings
    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );

    // A database connection
    private static $conn;

    // Connects to the database using given credentials
    public static function connect($host, $user, $password, $db_name = '') {
        try {
            if (!isset(self::$conn)) {
                $dsn = $host . ';dbname=' . $db_name;
                self::$conn = new PDO($dsn, $user, $password, self::$settings);
            }
            return false;
        } catch (Exception $e) {
            if ($e->getCode() == 1049) {
                header('Location: '  . URLROOT . '/config/setup.php');
                return false;
            }
            return $e;
        }

    }

    // Executes a query and returns the number of affected rows
    public static function query($query, $params = []) {
        $result = self::$conn->prepare($query);
        $result->execute($params);
        return $result->rowCount();
    }

    // Executes a query and returns the first row of the result
    public static function queryOne($query, $params = []) {
        $result = self::$conn->prepare($query);
        $result->execute($params);
        return $result->fetch();
    }

    // Executes a query and returns all resulting rows as an array of associative arrays
    public static function queryAll($query, $params = []) {
        $result = self::$conn->prepare($query);
        $result->execute($params);
        return $result->fetchAll();
    }

    // Insert into table array of params
    public static function insert($table, $params = array()) {
        return self::query(
            "INSERT INTO `$table` (`" .
                implode('`, `', array_keys($params)) .
                "`) VALUES (" . str_repeat('?,', sizeof($params) - 1) . "?)",
            array_values($params)
        );
    }

    // Return id of the last execution
    public static function getLastId() {
        return self::$conn->lastInsertId();
    }
}
