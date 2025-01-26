<?php

$VIEWS_DIR = __DIR__ . '/..';
$CONTROLLERS_DIR = __DIR__ . '/../../controllers';
$MODELS_DIR = __DIR__ . '/../../models';

$uri = rtrim($_SERVER['REQUEST_URI'], '/');
$root = '/' . @explode('/', $uri, 3)[1];

function route_view() {
    global $root;
    global $uri;

    switch ($root) {
        case '/archive': return '/archive';
        case '/profile': return '/profile';
        case '/register': return '/register';
        case '/login': return '/login';
        case '/logout': return '/logout';
        case '/newlist': return '/newlist';
        case '/list': return '/list';
    }

    switch ($uri) {
        case '': case '/': case '/home':
            return '/home';

        case '/authenticate':
            return '/profile/authenticate.php';

        default:
            http_response_code(404);
            return '/404';
    }
}
$view = $VIEWS_DIR . route_view();

require_once '../../models/database.php';
foreach (glob($MODELS_DIR . '/*.php') as $filename) {
    require_once $filename;
}

$TOKEN = (array_key_exists('token', $_COOKIE)) ? ($_COOKIE['token'] ?? "") : ("");

if (str_ends_with($view, '.php')) {
    require_once $view;
}
else {
    @include_once "$view/meta.php";

    if (isset($controller)) {
        require_once "$CONTROLLERS_DIR/$controller.php";
        require_once "$CONTROLLERS_DIR/meta.php";
    }

    require_once './header.php';
    require_once "$view/index.php";
    require_once './footer.php';
}
