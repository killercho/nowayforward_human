<section class="item">
    <section>
        <div>
            <img src="<?= '/archives/' . $page->FaviconPath ?>" class="favicon">
            <a href="<?= '/archives/' . $page->WID . '/index.php' ?>"><?= $page->URL ?></a>
            <span class="float-right"><?= $page->Date ?></span>
        </div>
        <div class="details">
            <span>Visits: <?= $page->Visits ?></span>
            <iframe id="printFrame" src="<?= '/archives/' . $page->WID . '/index.php' ?>" hidden sandbox></iframe>
            <span class="float-right"><?= Database\User::fromDBuid($page->RequesterUID)->Username ?></span>
        </div>
    </section>
    <section class="global-buttons">
        <form action="/list/export/index.php" method="GET">
            <input type="hidden" name="wid" value="<?= $page->WID ?>">
            <input type="hidden" name="title" value="<?= $page->Title ?>">
            <input type="hidden" name="url" value="<?= $page->URL ?>">
            <button><?php include $VIEWS_DIR . '/img/pdf-export.svg' ?></button>
        </form>
        <span></span>
    </section>
    <section name="itemButton" hidden>
        <form action="/list/add" method="GET">
            <input type="hidden" name="wid" value="<?= $page->WID ?>">
            <button><?php include $VIEWS_DIR . '/img/add-list.svg' ?></button>
        </form>
        <?php if ($user !== null && $user->Role === 'Admin'): ?>
            <form action="/archive/delete" method="GET">
                <input type="hidden" name="wid" value="<?= $page->WID ?>">
                <button><?php include $VIEWS_DIR . '/img/delete.svg' ?></button>
            </form>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
    </section>
</section>
