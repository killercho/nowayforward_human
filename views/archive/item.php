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
        <form action="/list/add" method="GET">
            <input type="hidden" name="wid" value="<?= $page->WID ?>">
            <button>
                <!-- Tabler icons https://tabler.io/icons -->
                <svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="list-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v5" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
            </button>
        </form>
        <span><!-- Delete (when admin) button --></span>
    </section>
</section>
