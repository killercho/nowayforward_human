<?php
namespace Database;
use PDO;

class ArchiveList extends Table {
    public $LID;
    public $AuthorUID;
    public $Name;
    public $Description;

    static function create(int $AuthorUID, string $Name, string $Description) : int {
        return Table::_create(
            'ArchiveLists',
            '(AuthorUID, Name, Description)',
            "(\"$AuthorUID\", \"$Name\", \"$Description\")"
        );
    }

    static function fromDB(int $LID) : ArchiveList {
        return Table::_fromDB(
            "SELECT * FROM ArchiveLists WHERE LID = \"$LID\"",
            'Database\ArchiveLists'
        );
    }

    static function allListsByUser(int $UID) : array {
        return Table::_get_all(
            'ArchiveLists',
            'Database\ArchiveLists',
            "WHERE AuthorUID = \"$UID\""
        );
    }
}
