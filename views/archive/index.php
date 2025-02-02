<?php
    $exists = null;
    $page = null;
    $user = null;

    try {
        list($exists, $url) = doesWebsiteExist($url);
        normalizeUrl($url);

        $page = Database\Webpage::fromDB($url);
        $page->incrementVisits();

        $user = Database\Cookie::fromDB($TOKEN);
    }
    catch(Exception $e) {
    }
?>

<?php if ($page !== null): ?>
    <iframe src="<?= "/archives/{$page->WID}/index.php" ?>" scrolling="no"></iframe>

    <form action="/archive/create" method="POST">
        <input type="hidden" name="url" value="<?= $url ?>">
        <input type="submit" value="Archive Now!">
    </form>
    <!-- Button to add to list -->
    <!-- Button to delete -->

    <h2>Archives by date:</h2>
    <?php
        foreach ($page->allArchives() as $page) {
            include __DIR__ . '/item.php';
        }
        include_once __DIR__ . '/item_show.php';
    ?>

<?php elseif(!$exists): ?>
    <h2>"<?= $url ?>" Does not exist!</h2>
    <p>Submit another request or check the spelling of the site and try again</p>
    <a href="/">Go back!</a>


<?php else: ?>
    <h2>"<?= $url ?>" hasn't been archived yet!</h2>
    <form action="/archive/create" method="POST">
        <input type="hidden" name="url" value="<?= $url ?>">
        <input type="submit" value="Archive Now!">
    </form>

<?php endif; ?>
