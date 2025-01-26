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
            Fail: <?= $status ?>
        </p>
    <?php else: ?>
        <p>
            Success!
        </p>
        <script type="text/javascript">
            sessionStorage.setItem("token", "<?= $token ?>");
            window.location.href = "/home/index.php";
        </script>
    <?php endif; ?>
<?php endif; ?>

<form action="./index.php" method="POST">
    <input type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*">
    <input type="password" name="password" placeholder="Password" minlength="4">
    <input type="submit" value="Login">
</form>

