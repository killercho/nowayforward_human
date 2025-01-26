<?php

function call_handler(string $name) {
    if (function_exists($name)) {
        call_user_func($name);
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST': call_handler('Controller\on_post'); break;
    case 'PUT': call_handler('Controller\on_put'); break;
    case 'DELETE': call_handler('Controller\on_delete'); break;
};
