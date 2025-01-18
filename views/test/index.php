<?php
    include '../meta.php';
    print_r(Database\User::get_all());
?>

<form action="/test1/index.php" method="POST">
    <input type="text" name="Username" placeholder="Username">
    <input type="submit">
</form>

<?php end_page(); ?>
