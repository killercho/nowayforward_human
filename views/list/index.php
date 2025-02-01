<?php
    $list = null;
    $author = null;

    try {
        $list = Database\ArchiveList::fromDB($lid ?? -1);
        $author = Database\User::fromDBuid($list->AuthorUID);
    }
    catch(Exception $e) {}
?>

<?php if ($list !== null): ?>
    <section>
        <p><?= $list->Name ?></p>
        <p><?= $list->Description ?></p>
        <p><?= $author->Username ?></p>
    </section>
    <?php
        foreach ($list->allItems() as $page) {
            include $VIEWS_DIR . '/archive/item.php';
        }
        include_once $VIEWS_DIR . '/archive/item_show.php';
    ?>

<?php else: ?>
    <p>
        List doesn't exist
    </p>
<?php endif; ?>
