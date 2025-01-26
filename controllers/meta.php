<?php

function call_handler(string $name) {
    if (function_exists($name)) {
        call_user_func($name);
    }
}

$TOKEN = (array_key_exists('token', $_COOKIE)) ? ($_COOKIE['token'] ?? "") : ("");

function request_handler() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (array_key_exists('method', $_POST)) {
            switch ($_POST['method']) {
                case 'PUT': call_handler('Controller\on_put'); return;
                case 'DELETE': call_handler('Controller\on_delete'); return;
                case 'PATCH': call_handler('Controller\on_patch'); return;
            }
        }
        call_handler('Controller\on_post');
    }
}
request_handler();
