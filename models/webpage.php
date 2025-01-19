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

    static function create(string $Path, string $URL, int $RequesterUID) : int {
        return Table::_create(
            'Webpages',
            '(Path, URL, Date, Visits, RequesterUID)',
            "(\"$Path\", \"$URL\", (NOW() + INTERVAL 2 HOUR), 0, \"$RequesterUID\")"
        );
    }

    static function fromDB(string $URL) : Webpage {
        return Table::_fromDB(
            "SELECT * FROM Webpages WHERE URL = \"$URL\" ORDER BY Date DESC LIMIT 1",
            "Database\Webpage"
        );
    }

    static function mostVisited(int $count) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "GROUP BY URL ORDER BY Visits DESC, Date DESC LIMIT $count",
            'WID,Path,URL,Date,MAX(Visits) as Visits,RequesterUID'
        );
    }

    static function allArchives(string $URL) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "WHERE URL = \"$URL\" ORDER BY Date DESC"
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
