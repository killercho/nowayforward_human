<?php
    $user = require_login();
    $webpage = null;
    $list = null;

    try {
        $list = $user->archiveLists();
        $webpage = Database\Webpage::fromDBwid($_GET['wid']);
    }
    catch (Exception $e) {}
?>

<?php if ($webpage !== null && $list !== null && $list): ?>
    <h2>
        To which list do you want to add</br>
        <?= $webpage->URL ?></br>
        from</br>
        <?= $webpage->Date ?>?
    </h2>

    <form action="#" method="POST" class="font-125 flex-row width-100 center-margin">
        <input type="hidden" name="method" value="PATCH">
        <?php if (isset($list_status)): ?>
            <?php if ($list_status !== ""): ?>
                <p class="item error"><span>
                    <strong>Error:</strong> <?= $list_status ?>
                </span></p>
            <?php endif; ?>
        <?php endif; ?>
        <select name="lid" class="flex-expand">
            <?php foreach ($list as $list): ?>
                <option value="<?= $list->LID ?>"><?= $list->Name ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="type" value="add">
        <input type="hidden" name="wid" value="<?= $_GET['wid'] ?>">
        <input type="submit" value="Select">
    </form>

<?php elseif ($webpage === null): ?>
    <h2>No page with identifier <?= $_GET['wid'] ?> exists!</h2>

<?php else: ?>
    <h2>You have no lists!</h2>

    <form action="/list/new" method="GET">
        <input type="submit" value="Create a new list">
    </form>

<?php endif; ?>
