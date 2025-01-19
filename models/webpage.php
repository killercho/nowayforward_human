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
            "(\"$Path\", \"$URL\", NOW(), 0, \"$RequesterUID\")"
        );
    }

    static function fromDB(string $URL) : Webpage {
        return Table::_fromDB(
            "SELECT * FROM Webpages WHERE URL = \"$URL\"",
            "Database\Webpage"
        );
    }

    static function mostVisited(int $count) : array {
        return Table::_get_all(
            'Webpages',
            'Database\Webpage',
            "GROUP BY URL ORDER BY Visits DESC, Date DESC LIMIT $count"
        );
    }
}
