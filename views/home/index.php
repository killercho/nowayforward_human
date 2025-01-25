<?php
    include '../meta.php';
?>

<section class="highlight separate-margin">
    <h2>Explore the archives or add a new page</h2>
    <form action="/archive/index.php" method="GET" class="font-125 flex-row width-100 center-margin">
        <input type="text" name="page_url" placeholder="Enter a URL" class="flex-expand">
        <input type="submit" value="Search">
    </form>
</section>

<hr class="new-section"/>

<h1>Most popular archives</h1>

<?php foreach(Database\Webpage::mostVisited(10) as $page): ?>
    <section class="card" onclick="open_archive('<?php echo $page->URL ?>')">
        <section class="quickinfo">
            <a href="<?php echo $page->URL ?>"><?php echo $page->URL ?></a>
            <span class="float-right"><?php echo $page->Date ?></span>
        </section>
        <section class="title">
            <img src="<?php echo '/archives/' . $page->FaviconPath ?>"></span>
            <span><?php echo $page->Title ?></span>
        </section>
        <section>
            <strong>Visits: <?php echo $page->Visits ?></strong>
            <strong><!-- Archives count --></strong>
        </section>
    </section>
<?php endforeach; ?>
<h1>...</h1>

<div class="card-blank-afterspace"></div>

<script type="text/javascript">
function open_archive(url) {
    window.location.href = '/archive/index.php?page_url=' + url;
}
</script>

<?php end_page(); ?>
