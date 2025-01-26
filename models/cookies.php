<?php
namespace Database;
use PDO;

class Cookie extends Table {
    public $UID;
    public $Token;
    public $Expires;

    static function create(string $UID) : string {
        $Token = uuidv4();
        Table::_create(
            "Cookies",
            "(UID, Token, Expires)",
            "(\"$UID\", \"$Token\", NOW() + INTERVAL 30 DAY)",
        );
        return $Token;
    }

    static function fromDB(string $token) : User {
        $UID = Table::_fromDB(
            "SELECT UID FROM Cookies WHERE Token = \"$token\"",
            "Database\Cookie"
        )->UID;
        return User::fromDBuid($UID);
    }

    static function delete(string $token) {
        Table::_delete(
            'Cookies',
            "Token = \"$token\""
        );
    }
}

// Taken from https://stackoverflow.com/a/15875555
function uuidv4()
{
  $data = random_bytes(16);

  $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
