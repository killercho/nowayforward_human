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

function on_put() {
    global $TOKEN;
    global $METHOD;
    global $list_status;

    $list = null;
    try {
        $list = Database\ArchiveList::fromDB($METHOD['lid']);
    }
    catch(Exception $e) {
        $list_status = "This list doesn't exist!";
        return;
    }

    try {
        $user = Database\Cookie::fromDB($TOKEN);
        $author = Database\User::fromDBuid($list->AuthorUID);
        if ($author->UID !== $user->UID) {
            $list_status = "You're not the owner of this list! You have no permission to edit it!";
            return;
        }
    }
    catch(Exception $e) {
        $list_status = "Either your cookie is invalid or the author of this list has deleted their account!";
        return;
    }

    $list->update($METHOD['name'], $METHOD['description']);

    header('Location: /list/' . $list->LID);
    exit();
}

function on_delete() {
    global $TOKEN;
    global $METHOD;
    global $list_status;

    $list = null;
    try {
        $list = Database\ArchiveList::fromDB($METHOD['lid']);
    }
    catch(Exception $e) {
        $list_status = "This list doesn't exist!";
        return;
    }

    try {
        $user = Database\Cookie::fromDB($TOKEN);
        $author = Database\User::fromDBuid($list->AuthorUID);
        if ($author->UID !== $user->UID) {
            $list_status = "You're not the owner of this list! You have no permission to delete it!";
            return;
        }
    }
    catch(Exception $e) {
        $list_status = "Either your cookie is invalid or the author of this list has deleted their account!";
        return;
    }

    $list->delete();

    header('Location: /');
    exit();
}
