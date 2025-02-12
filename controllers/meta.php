<?php

include_once __DIR__ . '/../constants.php';

function call_handler(string $name) {
    if (function_exists($name)) {
        call_user_func($name);
    }
}

$TOKEN = (array_key_exists('token', $_COOKIE)) ? ($_COOKIE['token'] ?? "") : ("");

$METHOD = null;
if (array_key_exists('method', $_POST)) {
    $METHOD = $_POST;
}
else if (array_key_exists('method', $_GET)) {
    $METHOD = $_GET;
}

if ($METHOD !== null) {
    switch ($METHOD['method']) {
        case 'PUT': call_handler('Controller\on_put'); break;
        case 'DELETE': call_handler('Controller\on_delete'); break;
        case 'PATCH': call_handler('Controller\on_patch'); break;
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    call_handler('Controller\on_post');
}
