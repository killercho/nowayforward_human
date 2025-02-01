<?php
    require_once "../../models/database.php";
    require_once "../../models/webpage.php";
    require_once "../../models/user.php";

    $currentPageId = explode('/', $_SERVER['REQUEST_URI'], 4)[2];
    $currentPage = Database\Webpage::fromDBwid($currentPageId);
    $requester = Database\User::fromDBuid($currentPage->RequesterUID);

    $previousPageId = $currentPage->previousPageId();
    $nextPageId = $currentPage->nextPageId();
?>

<div class="navbar">
    <div class="navbar-info">
        <span>Title: <?= $currentPage->Title ?></span>
        <span>Url: <?= $currentPage->URL ?></span>
        <span>Date of archival: <?= $currentPage->Date ?></span>
        <span>Visits: <?= $currentPage->Visits ?></span>
        <span>Requested by: <?= $requester->Username ?></span>
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
