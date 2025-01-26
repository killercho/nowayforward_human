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
            'Database\ArchiveList'
        );
    }

    static function allListsByUser(int $UID) : array {
        return Table::_get_all(
            'ArchiveLists',
            'Database\ArchiveList',
            "WHERE AuthorUID = \"$UID\""
        );
    }

    function addItem(int $WID) {
        Table::_create(
            'ArchiveListsWebpages',
            '(WID, LID, Position)',
            "(\"$WID\", \"$this->LID\", \"0\")"
        );
    }
}
