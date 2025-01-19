<?php
    include '../meta.php';
    $page = null;
    runController('archive');
?>

<?php if ($page !== null): ?>
    <iframe src="<?php echo "/archives/{$page->WID}" ?>" scrolling="no"></iframe>
    <h2>Archives by date:</h2>
    <?php foreach (Database\Webpage::allArchives($page->URL) as $page): ?>
        <section class="item">
            <section>
                <div>
                    <img src="<?php echo "/archives/{$page->WID}/favicon.ico" ?>" class="favicon">
                    <a href="<?php echo "/archives/{$page->WID}" ?>"><?php echo $page->URL ?></a>
                    <span class="float-right"><?php echo $page->Date ?></span>
                </div>
                <div class="details">
                    <span>Visits: <?php echo $page->Visits ?></span>
                    <span class="float-right"><?php echo Database\User::fromDBuid($page->RequesterUID)->Username ?></span>
                </div>
            </section>
        </section>
    <?php endforeach; ?>

<?php else: ?>
    <h2>"<?php echo $_GET["page_url"] ?>" hasn't been archived yet!</h2>
    <form action="/sample_archive/index.php" method="POST">
        <input type="hidden" name="page_url" value="<?php echo $_GET["page_url"] ?>">
        <input type="submit" value="Archive Now!">
    </form>

<?php endif; ?>
