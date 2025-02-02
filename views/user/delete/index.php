<?php
    $user = require_login();
    $to_delete = null;

    try {
        $to_delete = Database\User::fromDB($username);
    }
    catch(Exception $e) {}
?>

<?php if ($to_delete !== null && ($user->UID === $to_delete->UID || $user->Role === 'Admin')): ?>
    <h1>Are you sure you want to delete <?= $to_delete->Username ?>?</h1>

    <form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
        <input type="hidden" name="method" value="DELETE">
        <?php if (isset($user_status)): ?>
            <?php if ($user_status !== ""): ?>
                <p class="item error"><span>
                    <strong>Error:</strong> <?= $user_status ?>
                </span></p>
            <?php endif; ?>
        <?php endif; ?>

        <input type="hidden" name="uid" value="<?= $to_delete->UID ?>">
        <input type="submit" value="Delete forever!">
    </form>

<?php elseif ($to_delete === null): ?>
    <h2>The user "<?= $username ?>" doesn't exist!</h2>

<?php else: ?>
    <h2>You have no permission to delete <?= $to_delete->Username ?>!</h2>

<?php endif; ?>

