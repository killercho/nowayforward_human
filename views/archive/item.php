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
    <section name="itemButton" hidden>
        <form action="/list/update" method="GET">
            <input type="hidden" name="wid" value="<?= $page->WID ?>">
            <input type="submit" value="+">
        </form>
        <span><!-- Delete (when admin) button --></span>
    </section>
</section>
