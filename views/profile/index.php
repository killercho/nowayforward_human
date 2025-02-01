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
    <?php
        foreach ($user->archives() as $page) {
            include $VIEWS_DIR . '/archive/item.php';
        }
        include_once $VIEWS_DIR . '/archive/item_show.php';
    ?>
    </section>

    <section id="user-lists" hidden>
    <?php foreach($user->archiveLists() as $list): ?>
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
