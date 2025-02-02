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
    <section class="list-container">
        <section>
            <h2><?= $list->Name ?></h2>
            <p class="user-info">
                <?php $author->icon(); ?>
                <?= $author->Username ?>
            </p>
            <p><?= $list->Description ?></p>

            <section id="list-buttons" hidden>
                <form action="/list/update" method="GET">
                    <input type="hidden" name="lid" value="<?= $list->LID ?>">
                    <input type="submit" value="Update">
                </form>
                <form action="/list/delete" method="GET">
                    <input type="hidden" name="lid" value="<?= $list->LID ?>">
                    <input type="submit" value="Delete">
                </form>
            </section>
            <script type="text/javascript">
                function showListButtons() {
                    document.getElementById('list-buttons').hidden = false;
                }
                authenticated(showListButtons);
            </script>
        </section>
        <section>
            <?php
                foreach ($list->allItems() as $page) {
                    include $VIEWS_DIR . '/archive/item.php';
                }
                include_once $VIEWS_DIR . '/archive/item_show.php';
            ?>
        </section>
    </section>

<?php else: ?>
    <p>
        List doesn't exist
    </p>

<?php endif; ?>
