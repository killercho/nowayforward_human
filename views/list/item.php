<section class="list" onclick="window.location.href = '/list/<?= $list->LID ?>'">
    <section>
        <div class="heading">
            <h2>
                <?= $list->Name ?>
            </h2>
            <a href="<?= '/profile/' . $user->Username ?>" class="float-right">
                <?= $user->Username ?>
            </a>
        </div>
        <p>
            <?= $list->Description ?>
        </p>
    </section>
    <section name="itemButton" hidden>
        <span><!-- Edit button --></span>
        <span><!-- Delete button --></span>
    </section>
</section>
