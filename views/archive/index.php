<?php
    $exists = null;
    $page = null;
    $user = null;
    $archives = null;

    try {
        list($exists, $url) = doesWebsiteExist($url);
        normalizeUrl($url);

        $page = Database\Webpage::fromDB($url);
        $archives = $page->allArchives();
        $user = Database\Cookie::fromDB($TOKEN);
    }
    catch(Exception $e) {
    }
?>

<?php if ($page !== null): ?>
    <h1 id="page-header"><?= $page->URL ?></h1>

    <p id="page-info">
        Viewed <b><?= $page->totalViewCount() ?></b> times
        across <b><?= count($archives) ?></b> archives,
        created between
        <b><?php echo explode(' ', end($archives)->Date, 2)[0]; reset($archives); ?></b>
        and
        <b><?= explode(' ', current($archives)->Date, 2)[0] ?></b>
    </p>

    <section id="page-buttons">
        <span class="flex-expand"></span>
        <form action="/archive/create" method="POST">
            <input type="hidden" name="url" value="<?= $url ?>">
            <input type="submit" value="Archive Now!" class="standalone-button">
        </form>
        <form action="/list/add" method="GET">
            <input type="hidden" name="wid" value="<?= current($archives)->WID ?>">
            <input type="submit" value="Add to a list!" class="standalone-button">
        </form>
        <span class="flex-expand"></span>
    </section>

    <iframe src="<?= "/archives/{$page->WID}/index.php" ?>" scrolling="no" sandbox></iframe>

    <h2>Archives by date:</h2>
    <?php
        foreach ($archives as $page) {
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
