<?php
    $user = null;
    try {
        $user = Database\User::fromDB($username);
    }
    catch(Exception $e) {}
?>

<?php if ($user !== null): ?>
    <section>
        <!-- https://tabler.io/icons -->
        <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  fill="currentColor" ><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" /><path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" /></svg>
        <h1 class="username"><?= $user->Username ?></h1>
    </section>

    <div class="card-blank-afterspace"></div>

    <h2 onclick="openArchives()">Archives</h2>
    <h2 onclick="openLists()">Lists</h2>
    <section id="user-archives">
    <?php foreach (Database\Webpage::allArchivesByUser($user->UID) as $page): ?>
        <section class="item">
            <section>
                <div>
                    <img src="<?= '/archives/' . $page->FaviconPath ?>" class="favicon">
                    <a href="<?= '/archives/' . $page->WID ?>"><?= $page->URL ?></a>
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
    <?php endforeach; ?>
        <script type="text/javascript">
            function showButtons() {
                for (buttonset of document.getElementsByName('itemButton')) {
                    buttonset.hidden = false;
                }
            }
            authenticated(showButtons);
        </script>
    </section>

    <section id="user-lists" hidden>
    <?php foreach(Database\ArchiveList::allListsByUser($user->UID) as $list): ?>
        <section>
            <?= $list->Name ?>
            <?= $list->Description ?>
        </section>
    <?php endforeach; ?>
    </section>

    <script type="text/javascript">
        const userArchives = document.getElementById('user-archives');
        const userLists = document.getElementById('user-lists');

        function openArchives() {
            userArchives.hidden = false;
            userLists.hidden = true;
        }
        function openLists() {
            userArchives.hidden = true;
            userLists.hidden = false;
        }
    </script>

<?php else: ?>
    <h2>User "<?= $username ?>" doesn't exist!</h2>
<?php endif; ?>
