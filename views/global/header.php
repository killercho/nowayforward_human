<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2b2b2e">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles.css">
    <title><?= htmlspecialchars($title ?? "No Way Forward Human");?></title>
</head>
<body>
    <script type="text/javascript" src="/authenticate.js"></script>
    <header>
        <nav id="global-nav">
            <div class="fadeout-left"></div>
            <a href="/">Home</a>
            <div class="flex-expand"></div>
            <a id="login" href="/login">Login</a>
            <a id="register" href="/register">Register</a>
            <a id="user" href="/user" hidden>Profile</a>
            <a id="logout" href="/logout" hidden>Logout</a>
            <div class="fadeout-right"></div>
        </nav>
        <script type="text/javascript">
            function updateNavbar(response) {
                document.getElementById('login').hidden = true;
                document.getElementById('register').hidden = true;

                document.getElementById('logout').hidden = false;
                const user = document.getElementById('user');
                user.hidden = false;
                user.href += '/' + response;
            }
            authenticated(updateNavbar);
        </script>
    </header>
    <article>
