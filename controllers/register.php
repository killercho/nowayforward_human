<?php

function on_post() {
    global $status;
    $status = "";
    try {
        Database\User::fromDB($_POST["username"]);
        $status = "User \"" . $_POST["username"] . "\" already exists!";
        return;
    }
    catch(Exception $e) {}

    try {
        Database\User::create($_POST["username"], $_POST["password"], "User");
    }
    catch(Exception $e) {
        $status = $e;
    }
}
