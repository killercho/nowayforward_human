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

    function icon() {
        global $VIEWS_DIR;
        // https://tabler.io/icons
        if ($this->Role === 'User') {
            include $VIEWS_DIR . '/img/user.svg';
        }
        else {
            include $VIEWS_DIR . '/img/user-star.svg';
        }
    }

    private static $AnonUID = 1;

    function update(string $Username, string $Password = null) {
        // Applicable to Anon user
        if ($this->Password === '') {
            throw new Exception('Not modifying system account!');
        }

        $Password = ($Password === null) ? $this->Password : password_hash($Password, PASSWORD_BCRYPT);
        Table::_update(
            'Users',
            "Username = \"$Username\", Password = \"$Password\"",
            "UID = \"$this->UID\""
        );
    }

    function delete() {
        // Applicable to Anon user
        if ($this->Password === '') {
            throw new Exception('Not deleting system account!');
        }

        Table::_update(
            'Webpages',
            'RequesterUID = "' . self::$AnonUID . '"',
            "RequesterUID = \"$this->UID\""
        );

        Table::_update(
            'ArchiveLists',
            'AuthorUID = "' . self::$AnonUID . '"',
            "AuthorUID = \"$this->UID\""
        );

        Table::_delete(
            'Users',
            "UID = \"$this->UID\""
        );
    }
}
