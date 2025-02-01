<section class="highlight separate-margin">
    <h2>Explore the archives or add a new page</h2>

    <form action="/archive" method="GET" class="font-125 flex-row width-100 center-margin">
        <input type="text" name="url" placeholder="Enter a URL" class="flex-expand">
        <input type="submit" value="Search">
    </form>
</section>

<hr class="new-section"/>

<h1>Most popular archives</h1>

<?php
    foreach(Database\Webpage::fromDBmostVisited(10) as $page) {
        $goto = "/archive/?url=$page->URL";
        include __DIR__ . '/card.php';
    }
?>

<h1>...</h1>

<div class="card-blank-afterspace"></div>
