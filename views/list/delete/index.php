<?php
    $user = require_login();
    $list = null;
    $author = null;

    try {
        $list = Database\ArchiveList::fromDB($_GET['lid'] ?? -1);
        $author = Database\User::fromDBuid($list->AuthorUID);
    }
    catch(Exception $e) {}
?>

<?php if ($list !== null && ($user->UID === $author->UID || $user->Role === 'Admin')): ?>

    <h1>Are you sure you want to delete <?= $author->Username ?>'s "<?= $list->Name ?>"?</h1>

    <form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
        <input type="hidden" name="method" value="DELETE">
        <?php if (isset($list_status)): ?>
            <?php if ($list_status !== ""): ?>
                <p class="item error"><span>
                    <strong>Error:</strong> <?= $list_status ?>
                </span></p>
            <?php endif; ?>
        <?php endif; ?>

        <input type="hidden" name="lid" value="<?= $_GET['lid'] ?>">
        <input type="submit" value="Delete forever!">
    </form>

<?php elseif ($list === null): ?>
    <h2>No list with identifier <?= $_GET['lid'] ?> exists!</h2>

<?php else: ?>
    <h2>You have no permission to delete <?= $user->Username ?>'s "<?= $list->Name ?>"!</h2>

<?php endif; ?>
