<section class="list" onclick="window.location.href = '/list/<?= $list->LID ?>'">
    <section>
        <div class="heading">
            <h2>
                <?= $list->Name ?>
            </h2>
            <a href="<?= '/user/' . $user->Username ?>" class="float-right">
                <?= $user->Username ?>
            </a>
        </div>
        <p>
            <?= $list->Description ?>
        </p>
    </section>
    <section name="itemButton" hidden>
        <?php if ($user !== null && $user->UID === $list->AuthorUID): ?>
            <form action="/list/update" method="GET">
                <input type="hidden" name="lid" value="<?= $list->LID ?>">
                <button><?php include $VIEWS_DIR . '/img/edit.svg' ?></button>
            </form>
        <?php endif; ?>
        <?php if ($user !== null && ($user->UID === $list->AuthorUID || $user->Role === 'Admin')): ?>
            <form action="/list/delete" method="GET">
                <input type="hidden" name="lid" value="<?= $list->LID ?>">
                <button><?php include $VIEWS_DIR . '/img/delete.svg' ?></button>
            </form>
        <?php endif; ?>
    </section>
</section>
