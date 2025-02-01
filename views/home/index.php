<section class="highlight separate-margin">
    <h2>Explore the archives or add a new page</h2>

    <form action="/archive" method="GET" class="font-125 flex-row width-100 center-margin">
        <input type="text" name="url" placeholder="Enter a URL" class="flex-expand">
        <input type="submit" value="Search">
    </form>
</section>

<hr class="new-section"/>

<h1>Most popular archives</h1>

<?php foreach(Database\Webpage::fromDBmostVisited(10) as $page): ?>
    <section class="card" onclick="goto_archive('<?= $page->URL ?>')">
        <section class="quickinfo">
            <a href="<?= $page->URL ?>"><?= $page->URL ?></a>
            <span class="float-right"><?= $page->Date ?></span>
        </section>
        <section class="title">
            <img src="<?= '/archives/' . $page->FaviconPath ?>"></span>
            <span><?= $page->Title ?></span>
        </section>
        <section>
            <strong>Visits: <?= $page->Visits ?></strong>
            <strong><!-- Archives count --></strong>
        </section>
        <script type="text/javascript">
            function open_archive(url) {
                window.location.href = '/archive/' + url;
            }
        </script>
    </section>
<?php endforeach; ?>

<h1>...</h1>

<div class="card-blank-afterspace"></div>

<script type="text/javascript">
    function goto_archive(uri) {
        window.location.href = '/archive/?url=' + uri;
    }
</script>
