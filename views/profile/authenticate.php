<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain');
    echo $_SERVER['REQUEST_METHOD'] . " request not allowed!";
    exit;
}

try {
    $headers = apache_request_headers();
    $user = Database\Cookie::fromDB($headers["Authorization"]);

    http_response_code(200);
    header('Content-Type: text/plain');
    echo $user->Username;
}
catch(Exception $e) {
    http_response_code(401);
    header('Content-Type: text/plain');
    echo 'Bad token!';
}

exit;
