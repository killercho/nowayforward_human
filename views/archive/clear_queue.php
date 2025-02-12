<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain');
    echo $_SERVER['REQUEST_METHOD'] . " request not allowed!";
    exit;
}

session_start();
session_unset();
session_destroy();
