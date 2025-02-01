<?php
namespace Controller;
use Database;
use Exception;

function on_post() {
    global $TOKEN;
    global $list_status;
    global $lid;
    $list_status = "";
    $lid = 0;

    try {
        $uid = Database\Cookie::fromDB($TOKEN)->UID;
        $lid = Database\ArchiveList::create($uid, $_POST["name"], $_POST["description"]);
    }
    catch(Exception $e) {
        $list_status = $e;
    }
}

function on_patch() {
    global $TOKEN;
    global $METHOD;

    try {
        $user = Database\Cookie::fromDB($TOKEN);
    }
    catch(Exception $e) {
        return;
    }

    $list = null;
    try {
        $list = Database\ArchiveList::fromDB($METHOD['lid']);
    }
    catch(Exception $e) {
        return;
    }

    switch ($METHOD['type']) {
        case 'add': $list->addItem($METHOD['wid']); break;

        default: throw new Exception('Unknown type ' . $METHOD['type']);
    }

    header('Location: /list/' . $list->LID);
    exit();
}
