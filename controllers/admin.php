<?php
namespace Controller;
use Database;
use Exception;

function on_patch() {
    global $TOKEN;
    global $METHOD;
    global $role_status;
    $role_status = "";

    $status = null;
    switch ($METHOD['type']) {
        case 'role': $status = 'role_status'; break;
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
        case 'role':
            try {
                $to_update = Database\User::fromDB($METHOD['username']);
                $to_update->update($to_update->Username, null, $METHOD['role']);
            }
            catch (Exception $e) {
                $$status = "User doesn't exist!";
                return;
            }
            break;
    }
}
