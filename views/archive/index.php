<?php
    include '../meta.php';
    $page = null;
    runController('archive');
?>

<?php if ($page !== null): ?>
    <iframe>
        <!-- /archives/$page->WID -->
    </iframe>
    <?php foreach (Database\Webpage::allArchives($page->URL) as $page): ?>
        <section>
            <?php echo $page->Date ?>
        </section>
    <?php endforeach; ?>

<?php else: ?>
    <h2>This page hasn't been archived yet!</h2>
    <form action="/sample_archive/index.php" method="POST">
        <input type="hidden" name="page_url" value="<?php echo $_GET["page_url"] ?>">
        <input type="submit" value="Archive Now!">
    </form>

<?php endif; ?>
