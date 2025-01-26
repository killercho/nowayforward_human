<?php
    $user = null;
    try {
        $user = Database\User::fromDB($username);
    }
    catch(Exception $e) {}
?>

<?php if ($user !== null): ?>
    <section>
        <!-- https://tabler.io/icons -->
        <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  fill="currentColor" ><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" /><path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" /></svg>
        <h1 class="username"><?= $user->Username ?></h1>
    </section>
<?php else: ?>
    <h2>User "<?= $username ?>" doesn't exist!</h2>
<?php endif; ?>
