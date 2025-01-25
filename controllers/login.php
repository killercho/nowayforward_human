<?php

function on_post() {
    global $status;
    global $token;
    $status = "";

    try {
        $user = Database\User::fromDB($_POST["username"]);
        if (password_verify($_POST["password"], $user->Password)) {
            $token = Database\Cookie::create($user->UID);
        }
        else {
            $status = "Incorrect password!";
        }
    }
    catch(Exception $e) {
        $status = "User \"" . $_POST["username"] . "\" doesn't exist!";
    }
}
