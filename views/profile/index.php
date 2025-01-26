<?php
    $title = $_GET["user"] . ' - Profile';
    include '../meta.php';

    $user = null;
    runController('user');
?>

<?php if ($user !== null): ?>
    <section>
        <?= $user->Username ?>
        <?= $user->Role ?>
    </section>
<?php else: ?>
    <h2>User "<?= $_GET["user"] ?>" doesn't exist!</h2>
<?php endif; ?>

<?php end_page(); ?>
