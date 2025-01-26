<?php
    $title = 'Register a new user';
    include '../meta.php';

    $status = null;
    runController('register');
?>

<?php if ($status !== null): ?>
    <?php if ($status !== ""): ?>
        <p>
            Fail: <?= $status ?>
        </p>
    <?php else: ?>
        <p>
            Success!
        </p>
    <?php endif; ?>
<?php endif; ?>

<form action="./index.php" method="POST">
    <input type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*">
    <input type="password" name="password" placeholder="Password" minlength="4">
    <input type="submit" value="Register">
</form>
