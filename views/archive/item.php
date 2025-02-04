<section class="item">
    <section>
        <div>
            <img src="<?= '/archives/' . $page->FaviconPath ?>" class="favicon">
            <a href="<?= '/archives/' . $page->WID . '/index.php' ?>"><?= $page->URL ?></a>
            <span class="float-right"><?= $page->Date ?></span>
        </div>
        <div class="details">
            <span>Visits: <?= $page->Visits ?></span>
            <iframe id="printFrame" src="<?= '/archives/' . $page->WID . '/index.php' ?>" style="display:none;"></iframe>
            <span class="float-right"><?= Database\User::fromDBuid($page->RequesterUID)->Username ?></span>
            <form action="/list/export/index.php" method="GET">
                <input type="hidden" name="wid" value="<?= $page->WID ?>">
                <input type="hidden" name="title" value="<?= $page->Title ?>">
                <input type="hidden" name="url" value="<?= $page->URL ?>">
                <button>Export to PDF</button>
            </form>
        </div>
    </section>
    <section name="itemButton" hidden>
        <form action="/list/add" method="GET">
            <input type="hidden" name="wid" value="<?= $page->WID ?>">
            <button>
                <!-- Tabler icons https://tabler.io/icons -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="list-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v5" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
            </button>
        </form>
        <?php if ($user !== null && $user->Role === 'Admin'): ?>
            <form action="/archive/delete" method="GET">
                <input type="hidden" name="wid" value="<?= $page->WID ?>">
                <button>
                    <!-- Tabler icons https://tabler.io/icons -->
                    <svg  xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="list-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                </button>
            </form>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
    </section>
</section>
