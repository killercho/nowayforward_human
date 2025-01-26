<?php if (isset($user_status)): ?>
    <?php if ($user_status !== ""): ?>
        <p>
            Fail: <?= $user_status ?>
        </p>
    <?php else: ?>
        <p>
            Success!
        </p>
    <?php endif; ?>
<?php endif; ?>

<form action="#" method="POST">
    <input type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*">
    <input type="password" name="password" placeholder="Password" minlength="4">
    <input type="submit" value="Register">
</form>
