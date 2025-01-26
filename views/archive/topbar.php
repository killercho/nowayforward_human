<!-- Dirty hack to escape all PHP dom sanitization and checks -->
</script>

<?php
    require_once "../../models/database.php";
    require_once "../../models/webpage.php";
    require_once "../../models/user.php";

    $currentPageId = basename(__DIR__);
    $currentPage = Database\Webpage::fromDBwid($currentPageId);
    $requesterUsername = Database\User::fromDBuid($currentPage->RequesterUID);

    $previousPageId = Database\Webpage::getPreviousPageId($currentPage->URL, $currentPage->Date);
    $nextPageId = Database\Webpage::getNextPageId($currentPage->URL, $currentPage->Date);
?>

<div class="navbar">
    <div class="navbar-info">
        <span>Title: <?= $currentPage->Title ?></span>
        <span>Url: <?= $currentPage->URL ?></span>
        <span>Date of archival: <?= $currentPage->Date ?></span>
        <span>Visits: <?= $currentPage->Visits ?></span>
        <span>Requested by: <?= $requesterUsername->Username ?></span>
    </div>

    <div class="navbar-links">
        <? if ($previousPageId != 0): ?>
            <a href="<?= "../$previousPageId/index.php" ?>">Previous version</a>
        <? endif; ?>
        <? if ($nextPageId != 0): ?>
            <a href="<?= "../$nextPageId/index.php" ?>">Next version</a>
        <? endif; ?>
    </div>
</div>
