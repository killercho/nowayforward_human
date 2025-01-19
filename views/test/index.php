<?php
    include '../meta.php';
?>

<form action="/test1/index.php" method="POST">
    <input type="text" name="Username" placeholder="Username">
    <input type="submit">
</form>

<?php foreach(Database\User::get_all() as $user):?>
    <section class="card">
        <section class="quickinfo">
            <?php print_r($user); ?>
        </section>
        <strong><?php echo $user->Username ?></strong>
        <em><?php echo $user->Role ?></em>
    </section>
<?php endforeach;?>

<?php end_page(); ?>
