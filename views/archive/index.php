<?php
    $exists = null;
    $page = null;

    try {
        list($exists, $url) = Controller\doesWebsiteExist($url);
        Controller\normalizeUrl($url);

        $page = Database\Webpage::fromDB($url);
        $page->incrementVisits();
    }
    catch(Exception $e) {
    }
?>

<?php if ($page !== null): ?>
    <iframe src="<?= "/archives/{$page->WID}/index.php" ?>" scrolling="no"></iframe>

    <form action="#" method="POST">
        <input type="hidden" name="page_url" value="<?= $url ?>">
        <input type="submit" value="Archive Now!">
    </form>
    <!-- Button to add to list -->
    <!-- Button to delete -->

    <h2>Archives by date:</h2>
    <?php foreach (Database\Webpage::allArchives($page->URL) as $page): ?>
        <section class="item">
            <section>
                <div>
                    <img src="<?= '/archives/' . $page->FaviconPath ?>" class="favicon">
                    <a href="<?= '/archives/' . $page->WID . '/index.php' ?>"><?= $page->URL ?></a>
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

<?php elseif(!$exists): ?>
    <h2>"<?= $url ?>" Does not exist!</h2>
    <p>Submit another request or check the spelling of the site and try again</p>
    <a href="/">Go back!</a>


<?php else: ?>
    <h2>"<?= $url ?>" hasn't been archived yet!</h2>
    <form action="#" method="POST">
        <input type="hidden" name="page_url" value="<?= $url ?>">
        <input type="submit" value="Archive Now!">
    </form>

<?php endif; ?>
