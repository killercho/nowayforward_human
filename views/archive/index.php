<?php
    $title = $_GET["page_url"] . ' archive';
    include '../meta.php';

    $page = null;
    runController('archive');
?>

<?php if ($page !== null): ?>
    <iframe src="<?= "/archives/{$page->WID}" ?>" scrolling="no"></iframe>

    <form action="/sample_archive/index.php" method="POST">
        <input type="hidden" name="page_url" value="<?= $_GET["page_url"] ?>">
        <input type="submit" value="Archive Now!">
    </form>
    <!-- Button to add to list -->
    <!-- Button to delete -->

    <h2>Archives by date:</h2>
    <?php foreach (Database\Webpage::allArchives($page->URL) as $page): ?>
        <section class="item">
            <section>
                <div>
                    <img src="<?= "/archives/{$page->WID}/favicon.ico" ?>" class="favicon">
                    <a href="<?= "/archives/{$page->WID}" ?>"><?= $page->URL ?></a>
                    <span class="float-right"><?= $page->Date ?></span>
                </div>
                <div class="details">
                    <span>Visits: <?= $page->Visits ?></span>
                    <span class="float-right"><?= Database\User::fromDBuid($page->RequesterUID)->Username ?></span>
                </div>
            </section>
            <?php if (false): # If user logged-in ?>
                <section>
                    <span><!-- Add to list button --></span>
                    <span><!-- Delete (when admin) button --></span>
                <section>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>

<?php elseif(!doesWebsiteExist($_GET["page_url"])): ?>
    <h2>"<?= $_GET["page_url"] ?>" Does not exist!</h2>
    <p>Submit another request or check the spelling of the site and try again</p>
    <a href="/home/index.php">Go back!</a>


<?php else: ?>
    <h2>"<?= $_GET["page_url"] ?>" hasn't been archived yet!</h2>
    <form action="/sample_archive/index.php" method="POST">
        <input type="hidden" name="page_url" value="<?= $_GET["page_url"] ?>">
        <input type="submit" value="Archive Now!">
    </form>

<?php endif; ?>
