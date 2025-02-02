<?php
namespace Controller;
use Database;
use Exception;

function on_post() {
    global $user_status;
    $user_status = "";

    try {
        Database\User::fromDB($_POST["username"]);
        $user_status = "User \"" . $_POST["username"] . "\" already exists!";
        return;
    }
    catch(Exception $e) {}

    try {
        Database\User::create($_POST["username"], $_POST["password"], "User");
    }
    catch(Exception $e) {
        $user_status = $e;
    }
}

function on_delete() {
    global $TOKEN;
    global $METHOD;
    global $user_status;
    $user_status = "";

    try {
        Database\Cookie::fromDB($TOKEN);
    }
    catch (Exception $e) {
        $user_status = 'Invalid token!';
        return;
    }

    $to_delete = null;
    try {
        $to_delete = Database\User::fromDBuid($METHOD['uid']);
    }
    catch(Exception $e) {
        $list_status = "The user you're trying to delete doesn't exist!";
        return;
    }

    $to_delete->delete();

    header('Location: /');
    exit();
}
