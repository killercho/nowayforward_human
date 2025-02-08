<?php
namespace Controller;
use Database;
use Exception;

function on_post() {
    global $user_status;
    global $token;
    $user_status = "";

    try {
        Database\User::fromDB($_POST["username"]);
        $user_status = "User \"" . $_POST["username"] . "\" already exists!";
        return;
    }
    catch(Exception $e) {}

    try {
        $uid = Database\User::create($_POST["username"], $_POST["password"], "User");
        $token = Database\Cookie::create($uid);
    }
    catch(Exception $e) {
        $user_status = $e;
    }
}

function on_patch() {
    global $TOKEN;
    global $METHOD;
    global $username_status;
    global $password_status;
    $username_status = "";
    $password_status = "";

    $status = null;
    switch ($METHOD['type']) {
        case 'username': $status = 'username_status'; break;
        case 'password': $status = 'password_status'; break;
        default: throw new Exception('Invalid patch type ' . $METHOD['type']);
    }

    $user = null;
    try {
        $user = Database\Cookie::fromDB($TOKEN);
    }
    catch(Exception $e) {
        $$status = "Couldn't retrieve user!";
        return;
    }

    switch ($METHOD['type']) {
        case 'username':
            $user->update($METHOD['username']);
            header('Location: /user/' . $METHOD['username']);
            break;
        case 'password':
            $user->update($user->Username, $METHOD['password']);
            header('Location: /user/' . $user->Username);
            break;
    }
    exit();
}

function on_delete() {
    global $TOKEN;
    global $METHOD;
    global $user_status;
    $user_status = "";

    $user = null;
    try {
        $user = Database\Cookie::fromDB($TOKEN);
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

    if ($user->UID !== $to_delete->UID && $user->Role !== 'Admin') {
        $list_status = 'You have no permission to delete this user!';
        return;
    }

    $to_delete->delete();

    header('Location: /');
    exit();
}
