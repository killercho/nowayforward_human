<section class="card" onclick="window.location.href = '<?= $goto ?>'">
    <section class="quickinfo">
        <a href="<?= $page->URL ?>"><?= $page->URL ?></a>
        <span class="float-right"><?= $page->Date ?></span>
    </section>
    <section class="title">
        <img src="<?= '/archives/' . $page->FaviconPath ?>"></span>
        <span><?= $page->Title ?></span>
    </section>
    <section>
        <strong>Visits: <?= $page->totalViewCount() ?></strong>
        <strong>Archives: <?= count($page->allArchives()) ?></strong>
    </section>
</section>

