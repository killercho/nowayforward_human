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

    static function getPreviousPageId(string $url, string $date) : int {
        $foundId = Table::_get_all(
            "Webpages",
            "Database\Webpage",
            "WHERE URL = \"$url\" && Date < \"$date\" ORDER BY Date DESC LIMIT 1",
            "WID"
        );
        if (count($foundId) > 0) {
            return $foundId[0]->WID;
        }
        return 0;
    }

    static function getNextPageId(string $url, string $date) : int {
        $foundId = Table::_get_all(
            "Webpages",
            "Database\Webpage",
            "WHERE URL = \"$url\" && Date > \"$date\" ORDER BY Date ASC LIMIT 1",
            "WID"
        );
        if (count($foundId) > 0) {
            return $foundId[0]->WID;
        }
        return 0;
    }

    static function getPagesCount() : int {
        return Table::_get_entries_count("Webpages");
    }

    static function mostVisited(int $count) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "GROUP BY URL ORDER BY Visits DESC, Date DESC LIMIT $count",
            'WID,Path,URL,Date,MAX(Visits) as Visits,RequesterUID,FaviconPath,Title'
        );
    }

    static function allArchives(string $URL) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE URL = \"$URL\" ORDER BY Date DESC"
        );
    }

    static function allArchivesByUser(int $UID) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE RequesterUID = \"$UID\" ORDER BY Date DESC"
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

    function incrementVisits() {
        Table::_update(
            'Webpages',
            "Visits = \"" . ($this->Visits + 1) . "\"",
            "WID = \"{$this->WID}\""
        );
    }
}
