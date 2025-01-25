<?php
    $title = 'Login to your account';
    include '../meta.php';

    $status = null;
    $token = null;
    runController('login');
?>

<?php if ($status !== null): ?>
    <?php if ($status !== ""): ?>
        <p>
            Fail: <?php echo $status ?>
        </p>
    <?php else: ?>
        <p>
            Success! Token: <?php echo $token ?>
        </p>
    <?php endif; ?>
<?php endif; ?>

<form action="./index.php" method="POST">
    <input type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*">
    <input type="password" name="password" placeholder="Password" minlength="4">
    <input type="submit" value="Login">
</form>
