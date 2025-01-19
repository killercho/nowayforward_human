<?php
    $title = 'Archive a page';
    include '../meta.php';
    runController('archive_page');
?>

<form action="/sample_archive/index.php" method="POST">
    <input type="text" name="page_url">
    <input type="submit" name="archive_page_button">
</form>

<?php end_page(); ?>
