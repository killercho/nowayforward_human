<?php
namespace Database;
use PDO;

class User extends Table {
    public $UID;
    public $Username;
    public $Password;
    public $Role;

    static function create(string $Username, string $Password, string $Role) : int {
        return Table::_create(
            "Users",
            "(Username, Password, Role)",
            "(\"$Username\", \"$Password\", \"$Role\")",
        );
    }

    function fromDB(string $username) : User {
        return Table::_fromDB(
            "SELECT * FROM Users WHERE Username = \"$username\"",
            "Database\User"
        );
    }

    static function get_all() : array {
        return Table::_get_all("Database\User");
    }
}

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
        assert($query->rowCount() == 1, "Vaue for $class must be uniqely specified!");

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

    static protected function _get_all(string $class) : array {
        $conn = Table::connect();
        $query = $conn->query("SELECT * FROM Users");
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
