<?php
    $user = null;
    try {
        $user = Database\User::fromDB($username);
    }
    catch(Exception $e) {}
?>

<?php if ($user !== null): ?>
    <section>
        <?= $user->Username ?>
        <?= $user->Role ?>
    </section>
<?php else: ?>
    <h2>User "<?= $username ?>" doesn't exist!</h2>
<?php endif; ?>
