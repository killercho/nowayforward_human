<?php
    $user = null;
    $loggedin = null;
    try {
        $user = Database\User::fromDB($username);
        $loggedin = Database\Cookie::fromDB($TOKEN);
    }
    catch(Exception $e) {}
?>

<?php if ($user !== null): ?>
    <section>
        <span class="user-icon"><?php $user->icon(); ?></span>
        <h1 class="username"><?= $user->Username ?></h1>
    </section>

    <div class="user-blank-afterspace"></div>

    <section id="user-buttons" hidden>
        <?php if ($user !== null && $loggedin !== null && $user->UID === $loggedin->UID): ?>
            <form action="/list/new" method="GET">
                <input type="submit" value="Create a new list">
            </form>
            <form action="/user/settings" method="GET">
                <input type="submit" value="Account settings">
            </form>
            <?php if ($user->Role === 'Admin'): ?>
                <form action="/admin" method="GET">
                    <input type="submit" value="Admin panel">
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </section>
    <script type="text/javascript">
        function showUserButtons() {
            document.getElementById('user-buttons').hidden = false;
        }
        authenticated(showUserButtons);
    </script>

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
