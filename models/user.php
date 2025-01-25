<?php
namespace Database;
use PDO;

class User extends Table {
    public $UID;
    public $Username;
    public $Password;
    public $Role;

    static function create(string $Username, string $Password, string $Role) : int {
        $Password = password_hash($Password, PASSWORD_BCRYPT);
        return Table::_create(
            "Users",
            "(Username, Password, Role)",
            "(\"$Username\", \"$Password\", \"$Role\")",
        );
    }

    static function fromDB(string $username) : User {
        return Table::_fromDB(
            "SELECT * FROM Users WHERE Username = \"$username\"",
            'Database\User'
        );
    }

    static function fromDBuid(int $uid) : User {
        return Table::_fromDB(
            "SELECT * FROM Users WHERE UID = \"$uid\"",
            'Database\User'
        );
    }

    static function get_all() : array {
        return Table::_get_all("Users", "Database\User");
    }
}
