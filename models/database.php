<?php
namespace Database;
use PDO;
use Exception;

abstract class Table {
    // Cannot be created, because FETCH_CLASS will assign to all attributes
    // and then call the constructor
    final private function __construct() {}

    static protected function _fromDB(string $query_str, string $class) : object {
        $conn = Table::connect();
        $query = $conn->query($query_str);
        // TODO: research if this is enough to close the connection, $query may store a reference
        $conn = null;

        if ($query->rowCount() == 0) {
            throw new Exception("Value for $class doesn't exist!");
        }
        assert($query->rowCount() == 1, "Value for $class must be uniqely specified!");

        $query->setFetchMode(PDO::FETCH_CLASS, $class);
        return $query->fetch();
    }

    static protected function _create(string $table, string $columns, string $value) : int {
        $conn = Table::connect();
        $query = $conn->query("INSERT INTO $table $columns VALUES $value");

        // NOTE: If we ever insert more than one values, lastInsertId will returne the first id
        $id = $conn->lastInsertId();
        $conn = null;
        return $id;
    }

    static protected function _update(string $table, string $sets, string $identify) {
        $conn = Table::connect();
        $query = $conn->query("UPDATE $table SET $sets WHERE $identify");
        $conn = null;
    }

    static protected function _get_all(string $table, string $class, string $additional = null, $columns = "*") : array {
        $conn = Table::connect();
        $query = $conn->query("SELECT $columns FROM $table " . $additional);
        $conn = null;

        $query->setFetchMode(PDO::FETCH_CLASS, $class);
        return $query->fetchAll();
    }

    static protected function connect() : PDO {
        $conn = new PDO(
            "mysql:unix_socket=" . getenv('MYSQL_UNIX_SOCKET') . ";dbname=nwfh",
            getenv('USER'),
            "");
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
