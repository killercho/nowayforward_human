<?php
namespace Controller;
use Database;
use Exception;

function on_post() {
    global $TOKEN;
    global $list_status;
    $list_status = "";

    try {
        $uid = Database\Cookie::fromDB($TOKEN)->UID;
        Database\ArchiveList::create($uid, $_POST["name"], $_POST["description"]);
    }
    catch(Exception $e) {
        $list_status = $e;
    }
}
