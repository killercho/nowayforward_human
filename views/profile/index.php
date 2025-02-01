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

    <div class="user-blank-afterspace"></div>

    <nav id="user-nav">
        <button id="openArchives" onclick="openArchives()">Archives</button>
        <button id="openLists" onclick="openLists()" class="not-selected">Lists</button>
    </nav>
    <section id="userArchives">
    <?php
        foreach ($user->archives() as $page) {
            include $VIEWS_DIR . '/archive/item.php';
        }
        include_once $VIEWS_DIR . '/archive/item_show.php';
    ?>
    </section>

    <section id="userLists" hidden>
    <?php
        foreach ($user->archiveLists() as $list) {
            include $VIEWS_DIR . '/list/item.php';
        }
        include_once $VIEWS_DIR . '/list/item_show.php';
    ?>
    </section>

    <script type="text/javascript">
        const elemOpenArchives = document.getElementById('openArchives');
        const elemOpenLists = document.getElementById('openLists');
        const userArchives = document.getElementById('userArchives');
        const userLists = document.getElementById('userLists');

        function openArchives() {
            elemOpenArchives.classList.remove('not-selected');
            elemOpenLists.classList.add('not-selected');
            userArchives.hidden = false;
            userLists.hidden = true;
        }
        function openLists() {
            elemOpenArchives.classList.add('not-selected');
            elemOpenLists.classList.remove('not-selected');
            userArchives.hidden = true;
            userLists.hidden = false;
        }
    </script>

<?php else: ?>
    <h2>User "<?= $username ?>" doesn't exist!</h2>
<?php endif; ?>
