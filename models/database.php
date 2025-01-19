<?php
namespace Database;
use PDO;

class User {
    public $UID;
    public $Username;
    public $Password;
    public $Role;

    function fromDB(string $username) : User {
        $conn = connect();
        $query = $conn->query("SELECT * FROM Users WHERE Username = \"$username\"");
        // TODO: research if this is enough to close the connection, $query may store a reference
        $conn = null;

        if ($query->rowCount() == 0) {
            throw new Exception("User $username doesn't exist!");
        }
        assert($query->rowCount() == 1, "Users must have unique usernames!");

        $query->setFetchMode(PDO::FETCH_CLASS, "User");
        return $query->fetch();
    }

    static function create(string $Username, string $Password, string $Role) : int {
        $conn = connect();
        $query = $conn->query("INSERT INTO Users (Username, Password, Role) VALUES (\"$Username\", \"$Password\", \"$Role\")");

        // NOTE: If we ever insert more than one values, lastInsertId will returne the first id
        $id = $conn->lastInsertId();
        $conn = null;
        return $id;
    }

    static function get_all() : array {
        $conn = connect();
        $query = $conn->query("SELECT * FROM Users");
        $conn = null;

        $query->setFetchMode(PDO::FETCH_CLASS, "Database\User");
        return $query->fetchAll();
    }
}

function connect() : PDO {
    $conn = new PDO(
        "mysql:unix_socket=" . getenv('MYSQL_UNIX_SOCKET') . ";dbname=nwfh",
        getenv('USER'),
        "");
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}
