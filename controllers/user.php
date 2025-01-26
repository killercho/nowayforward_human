<?php

function on_get() {
    global $user;
    try {
        $user = Database\User::fromDB($_GET["user"]);
    }
    catch(Exception $e) {}
}

function on_post() {
    global $user;
    try {
        $headers = apache_request_headers();
        $user = Database\Cookie::fromDB($headers["Authorization"]);
    }
    catch(Exception $e) {}
}
