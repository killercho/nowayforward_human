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
        return Table::_get_all("Users", "Database\User");
    }
}
