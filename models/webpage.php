<?php
namespace Database;
use PDO;

class Webpage extends Table {
    public $WID;
    public $Path;
    public $URL;
    public $Date;
    public $Visits;
    public $RequesterUID;
    public $FaviconPath;
    public $Title;

    static function create(string $Path, string $URL, int $RequesterUID, ?string $FaviconPath, ?string $Title) : int {
        return Table::_create(
            'Webpages',
            '(Path, URL, Date, Visits, RequesterUID, FaviconPath, Title)',
            "(\"$Path\", \"$URL\", (NOW() + INTERVAL 2 HOUR), 0, \"$RequesterUID\", \"$FaviconPath\", \"$Title\")"
        );
    }

    static function fromDB(string $URL) : Webpage {
        return Table::_fromDB(
            "SELECT * FROM Webpages WHERE URL = \"$URL\" ORDER BY Date DESC LIMIT 1",
            "Database\Webpage"
        );
    }

    static function fromDBwid(int $WID) : Webpage {
        return Table::_fromDB(
            "SELECT * FROM Webpages WHERE WID = \"$WID\"",
            "Database\Webpage"
        );
    }

    // TODO: remove this, refer to archive.php
    // NOTE: Why remove? Let it be. Leave the thing alone
    static function getPagesCount() : int {
        return Table::_get_entries_count("Webpages");
    }

    static function updateNewArchive(int $WID, string $faviconPath, string $newTitle) : void {
        Table::_update("Webpages", "FaviconPath = \"$faviconPath\", Title = \"$newTitle\"", "WID = $WID");
    }

    static function fromDBmostVisited(int $count) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "GROUP BY URL ORDER BY Visits DESC, Date DESC LIMIT $count",
            'WID,Path,URL,Date,MAX(Visits) as Visits,RequesterUID,FaviconPath,Title'
        );
    }

    static function getArchivePathsByPattern(string $URLPattern) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE URL LIKE \"$URLPattern\" ORDER BY Date DESC",
            "Path, WID"
        );
    }

    function allArchives() : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE URL = \"$this->URL\" ORDER BY Date DESC"
        );
    }

    function previousPageId() : int {
        $foundId = Table::_get_all(
            "Webpages",
            "Database\Webpage",
            "WHERE URL = \"$this->URL\" AND Date < \"$this->Date\"
             ORDER BY Date DESC
             LIMIT 1",
            "WID"
        );
        if (count($foundId) > 0) {
            return $foundId[0]->WID;
        }
        return 0;
    }

    function nextPageId() : int {
        $foundId = Table::_get_all(
            "Webpages",
            "Database\Webpage",
            "WHERE URL = \"$this->URL\" AND Date > \"$this->Date\"
             ORDER BY Date ASC
             LIMIT 1",
            "WID"
        );
        if (count($foundId) > 0) {
            return $foundId[0]->WID;
        }
        return 0;
    }

    function incrementVisits() {
        Table::_update(
            'Webpages',
            "Visits = \"" . ($this->Visits + 1) . "\"",
            "WID = \"{$this->WID}\""
        );
    }

    function delete() {
        Table::_delete(
            'Webpages',
            "WID = \"$this->WID\""
        );
    }
}
