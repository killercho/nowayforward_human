
<!-- TODO: Redirect when no user -->

<?php
    $user = null;
    $webpage = null;
    $lists = null;

    try {
        $user = Database\Cookie::fromDB($TOKEN);
        $webpage = Database\Webpage::fromDBwid($_GET['wid']);
        $lists = Database\ArchiveList::allListsByUser($user->UID);
    }
    catch (Exception $e) {}
?>

<!-- TODO: Redirect when no webpage -->
<!-- TODO: Redirect when lists is empty -->

<h2>To which list do you want to add "<?= $webpage->URL ?>"?</h2>

<form action="/list" method="GET">
    <input type="hidden" name="method" value="PATCH">
    <select name="lid">
        <?php foreach ($lists as $list): ?>
            <option value="<?= $list->LID ?>"><?= $list->Name ?></option>
        <?php endforeach; ?>
    </select>
    <input type="hidden" name="type" value="add">
    <input type="hidden" name="wid" value="<?= $_GET['wid'] ?>">
    <input type="submit" value="Select">
</form>
