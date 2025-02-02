<h1>Register</h1>

<hr class="new-section"/>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <?php if (isset($user_status)): ?>
        <?php if ($user_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $user_status ?>
            </span></p>
        <?php else: ?>
            <p class="item success">
                Success!
            </p>
        <?php endif; ?>
    <?php endif; ?>

    <input type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*">
    <input type="password" name="password" placeholder="Password" minlength="4">
    <input type="submit" value="Register">
</form>
