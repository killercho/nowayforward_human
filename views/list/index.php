<?php
    $list = null;
    $author = null;

    try {
        $list = Database\ArchiveList::fromDB($lid);
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
<?php else: ?>
    <p>
        List doesn't exist
    </p>
<?php endif; ?>
