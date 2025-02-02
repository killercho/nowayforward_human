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

    function addItem(int $WID) {
        Table::_create(
            'ArchiveListsWebpages',
            '(WID, LID, Position)',
            "(\"$WID\", \"$this->LID\", \"0\")"
        );
    }

    function allItems() : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "INNER JOIN ArchiveListsWebpages ON Webpages.WID = ArchiveListsWebpages.WID
             WHERE ArchiveListsWebpages.LID = $this->LID
             ORDER BY ArchiveListsWebpages.Position ASC",
            'Webpages.*'
        );
    }

    function update(string $newName, string $newDescription) {
        Table::_update(
            'ArchiveLists',
            "Name = \"$newName\", Description = \"$newDescription\"",
            "LID = \"$this->LID\""
        );
    }

    function delete() {
        Table::_delete(
            'ArchiveLists',
            "LID = \"$this->LID\""
        );
    }
}
