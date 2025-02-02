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

<?php if ($list !== null && $user->UID === $author->UID): ?>

<h1>Update list</h1>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <input type="hidden" name="method" value="PUT">
    <?php if (isset($list_status)): ?>
        <?php if ($list_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $list_status ?>
            </span></p>
        <?php else: ?>
            <script type="text/javascript">
                window.location.href = '/list/<?= $_GET["lid"] ?>';
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <input type="hidden" name="lid" value="<?= $_GET['lid'] ?>">
    <input type="text" name="name" placeholder="List title" minlength="1" value="<?= $list->Name ?>">
    <textarea name="description" placeholder="Description"><?= $list->Description ?></textarea>
    <input type="submit" value="Update">
</form>

<?php elseif ($list === null): ?>
    <h2>No list with identifier <?= $_GET['lid'] ?> exists!</h2>

<?php else: ?>
    <h2>You're not the owner of "<?= $list->Name ?>", you have no permission to edit it!</h2>

<?php endif; ?>
