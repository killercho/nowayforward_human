<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain');
    echo $_SERVER['REQUEST_METHOD'] . " request not allowed!";
    exit;
}

include '../meta.php';

$user = null;
runController('user');

if ($user !== null) {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo $user->Username;
}
else {
    http_response_code(401);
    header('Content-Type: text/plain');
    echo 'Bad token!';
}

exit;
