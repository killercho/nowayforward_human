<?php
    require_login();

    $webpage = null;
    $list = null;

    try {
        $list = Database\Cookie::fromDB($TOKEN)->archiveLists();
        $webpage = Database\Webpage::fromDBwid($_GET['wid']);
    }
    catch (Exception $e) {}
?>

<?php if ($webpage !== null && $list !== null): ?>
    <h2>
        To which list do you want to add</br>
        <?= $webpage->URL ?></br>
        from</br>
        <?= $webpage->Date ?>?
    </h2>

    <form action="/list" method="GET" class="font-125 flex-row width-100 center-margin">
        <input type="hidden" name="method" value="PATCH">
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
