<?php

$VIEWS_DIR = __DIR__ . '/..';
$CONTROLLERS_DIR = __DIR__ . '/../../controllers';
$MODELS_DIR = __DIR__ . '/../../models';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/nowayforward_human/xampp/setup.php') {
    http_response_code(301); // Permanent redirect
    header('Location: /');
    exit;
}

$exploded = @explode('/', $uri, 4);
$root = '/' . @$exploded[1];
$subroot = '/' . @$exploded[2];

function route_view() {
    global $root;
    global $subroot;
    global $uri;

    switch ($uri) {
        case '': case '/': case '/home':
            return '/home';
    }

    switch ($root . $subroot) {
        case '/list/update': return '/list/update';
        case '/list/new': return '/list/new';
        case '/list/add': return '/list/add';
        case '/list/delete': return '/list/delete';

        case '/archive/create': return '/archive/create';
        case '/archive/delete': return '/archive/delete';

        case '/user/delete': return '/user/delete';
        case '/user/settings': return '/user/update';
    }

    switch ($root) {
        case '/archive': return '/archive';
        case '/user': return '/user';
        case '/register': return '/user/create';
        case '/login': return '/session/create';
        case '/logout': return '/session/delete';
        case '/list': return '/list';
        case '/admin': return '/admin';

        case '/authenticate':
            return '/user/authenticate.php';
    }

    http_response_code(404);
    return '/404';
}
$view = $VIEWS_DIR . route_view();

require_once '../../models/database.php';
foreach (glob($MODELS_DIR . '/*.php') as $filename) {
    require_once $filename;
}

$TOKEN = (array_key_exists('token', $_COOKIE)) ? ($_COOKIE['token'] ?? "") : ("");

function redirect(string $href) {
    echo '<script type="text/javascript">window.location.href = "' . $href . '";</script>';
    exit;
}

function require_login(string $redirect = '/login') : Database\User {
    global $TOKEN;
    if ($TOKEN === '') {
        redirect($redirect);
    }

    $user = null;
    try {
        $user = Database\Cookie::fromDB($TOKEN);
    }
    catch (Exception $e) {
        redirect($redirect);
    }
    return $user;
}

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
