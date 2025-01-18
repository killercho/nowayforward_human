<?php

foreach (glob("../models/*.php") as $filename) {
    include $filename;
}

function call_handler(string $name) {
    if (function_exists($name)) {
        call_user_func($name);
    }
}

match ($_SERVER['REQUEST_METHOD']) {
    'POST' => call_handler('on_post'),
    'GET'  => call_handler('on_get'),
    'PUT'  => call_handler('on_put'),
    'DELETE' => call_handler('on_delete'),
};
