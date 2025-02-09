<?php
    $user = require_login();
?>

<h1>Change your username</h1>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <input type="hidden" name="method" value="PATCH">
    <?php if (isset($username_status)): ?>
        <?php if ($username_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $username_status ?>
            </span></p>
        <?php endif; ?>
    <?php endif; ?>

    <input type="hidden" name="type" value="username">
    <input type="text" name="username" placeholder="New Username">
    <input type="submit" value="Update username">
</form>

<div class="user-blank-afterspace"></div>

<h1>Change your password</h1>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <input type="hidden" name="method" value="PATCH">
    <?php if (isset($password_status)): ?>
        <?php if ($password_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $password_status ?>
            </span></p>
        <?php endif; ?>
    <?php endif; ?>

    <input type="hidden" name="type" value="password">
    <input type="password" name="password" placeholder="New Password">
    <input type="submit" value="Update password">
</form>

<div class="user-blank-afterspace"></div>

<h1>Delete your account</h1>

<form action="/user/delete" method="GET" class="font-115 flex-col-centered max-width-20 center-margin">
    <input type="hidden" name="username" value="<?= $user->Username ?>">
    <input type="submit" value="Delete">
</form>
