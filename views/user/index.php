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
    <section id="user-main">
        <span class="user-icon"><?php $user->icon(); ?></span>
        <h1 class="username"><?= $user->Username ?></h1>

        <section id="user-buttons" hidden>
            <?php if ($user !== null && $loggedin !== null && $user->UID === $loggedin->UID): ?>
                <form action="/list/new" method="GET">
                    <button title="Create a new list" class="standalone-button"><?php include $VIEWS_DIR . '/img/list-add.svg' ?></button>
                </form>
                <form action="/user/settings" method="GET">
                    <button title="Account settings" class="standalone-button"><?php include $VIEWS_DIR . '/img/settings.svg' ?></button>
                </form>
                <?php if ($user->Role === 'Admin'): ?>
                    <form action="/admin" method="GET">
                        <button title="Global settings" class="standalone-button"><?php include $VIEWS_DIR . '/img/global-settings.svg' ?></button>
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
    </section>

    <div class="user-blank-afterspace"></div>

    <nav id="user-nav">
        <button id="openArchives" onclick="openArchives()">Archives</button>
        <button id="openLists" onclick="openLists()" class="not-selected">Lists</button>
    </nav>
    <section id="userArchives">
    <?php
        $archives = $user->archives();
        foreach ($archives as $page) {
            include $VIEWS_DIR . '/archive/item.php';
        }
        include_once $VIEWS_DIR . '/archive/item_show.php';
    ?>

    <?php if (count($archives) === 0): ?>
        <h1>No archives</h1>
    <?php endif; ?>
    </section>

    <section id="userLists" hidden>
    <?php
        $archiveLists = $user->archiveLists();
        foreach ($archiveLists as $list) {
            include $VIEWS_DIR . '/list/item.php';
        }
        include_once $VIEWS_DIR . '/list/item_show.php';
    ?>

    <?php if (count($archiveLists) === 0): ?>
        <h1>No lists</h1>
    <?php endif; ?>
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
            location.hash = 'archives';
        }
        function openLists() {
            elemOpenArchives.classList.add('not-selected');
            elemOpenLists.classList.remove('not-selected');
            userArchives.hidden = true;
            userLists.hidden = false;
            location.hash = 'lists';
        }

        if (location.hash.slice(1) === 'lists') {
            openLists();
        }
    </script>

<?php else: ?>
    <h2>User "<?= $username ?>" doesn't exist!</h2>
<?php endif; ?>
