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

    function archives() : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE RequesterUID = \"$this->UID\" ORDER BY Date DESC"
        );
    }

    function archiveLists() : array {
        return Table::_get_all(
            'ArchiveLists',
            'Database\ArchiveList',
            "WHERE AuthorUID = \"$this->UID\""
        );
    }
}
