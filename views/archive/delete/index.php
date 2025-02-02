<?php
    $user = require_login();
    $webpage = null;

    try {
        $webpage = Database\Webpage::fromDBwid($_GET['wid']);
    }
    catch (Exception $e) {}
?>

<?php if ($webpage !== null && $user->Role === 'Admin'): ?>
    <h1>Are you sure you want to delete <?= $webpage->URL ?> from <?= $webpage->Date ?>?</h1>

    <form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
        <input type="hidden" name="method" value="DELETE">
        <?php if (isset($page_status)): ?>
            <?php if ($page_status !== ""): ?>
                <p class="item error"><span>
                    <strong>Error:</strong> <?= $page_status ?>
                </span></p>
            <?php endif; ?>
        <?php endif; ?>

        <input type="hidden" name="wid" value="<?= $_GET['wid'] ?>">
        <input type="submit" value="Delete forever!">
    </form>

<?php elseif ($webpage === null): ?>
    <h2>No page with identifier <?= $_GET['wid'] ?> exists!</h2>

<?php else: ?>
    <h2>You have no permission to delete archives!</h2>

<?php endif; ?>
