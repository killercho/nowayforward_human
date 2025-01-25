<?php

function on_get() {
    global $user;
    try {
        $user = Database\User::fromDB($_GET["user"]);
    }
    catch(Exception $e) {}
}
