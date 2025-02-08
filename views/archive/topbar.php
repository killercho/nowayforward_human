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

<div class="nwfh-navbar">
    <div class="nwfh-navbar-info">
        <span>Title: <?= $currentPage->Title ?></span>
        <span>Url: <?= $currentPage->URL ?></span>
        <span>Date of archival: <?= $currentPage->Date ?></span>
        <span>Visits: <?= $currentPage->Visits ?></span>
        <span>Requested by: <?= $requester->Username ?></span>
    </div>

    <div class="nwfh-navbar-links">
        <? if ($previousPageId != 0): ?>
            <a href="<?= "../$previousPageId/index.php" ?>">Previous version</a>
        <? endif; ?>
        <? if ($nextPageId != 0): ?>
            <a href="<?= "../$nextPageId/index.php" ?>">Next version</a>
        <? endif; ?>
    </div>
</div>

<script type="text/javascript">
// If not in iframe
if (window.self === window.top) {
    var request = new XMLHttpRequest();
    request.open("POST", "/archive/visit.php", true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send('wid=' + <?= $currentPage->WID ?>);
}
</script>
