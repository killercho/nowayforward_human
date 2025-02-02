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
