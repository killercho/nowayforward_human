<?php
    $title = $_GET["user"] . ' - Profile';
    include '../meta.php';

    $user = null;
    runController('user');
?>

<?php if ($user !== null): ?>
    <section>
        <?php echo $user->Username ?>
        <?php echo $user->Role ?>
    </section>
<?php else: ?>
    <h2>User "<?php echo $_GET["user"] ?>" doesn't exist!</h2>
<?php endif; ?>
