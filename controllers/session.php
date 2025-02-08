<?php
namespace Controller;
use Database;
use Exception;

function on_post() {
    global $user_status;
    global $token;
    $user_status = "";

    try {
        $user = Database\User::fromDB($_POST["username"]);
        if (password_verify($_POST["password"], $user->Password)) {
            $token = Database\Cookie::create($user->UID);
        }
        else {
            $user_status = "Incorrect password!";
        }
    }
    catch(Exception $e) {
        $user_status = "User \"" . $_POST["username"] . "\" doesn't exist!";
    }
}

function on_delete() {
    global $TOKEN;
    try {
        $token = Database\Cookie::fromDBtoken($TOKEN);
        $token->delete();
    }
    catch(Exception $e) {}
}
