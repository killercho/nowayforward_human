<script type="text/javascript">
    if (!cookieStorage.getItem('token')) {
        window.location.href = '/login';
    }
</script>

<h1>Create a new list</h1>

<hr class="new-section"/>

<form action="#" method="POST">
    <?php if (isset($list_status)): ?>
        <?php if ($list_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $list_status ?>
            </span></p>
        <?php endif; ?>
    <?php endif; ?>

    <input type="text" name="name" placeholder="List title" minlength="1">
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Login">
</form>
