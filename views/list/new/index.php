<?php
    if (isset($list_status) && $list_status === "") {
    }
?>
<script type="text/javascript">
    if (!cookieStorage.getItem('token')) {
        window.location.href = '/login';
    }
</script>

<h1>Create a new list</h1>

<hr class="new-section"/>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <?php if (isset($list_status)): ?>
        <?php if ($list_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $list_status ?>
            </span></p>
        <?php else: ?>
            <script type="text/javascript">
                window.location.href = '/list/<?= $lid ?>';
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <input type="text" name="name" placeholder="List title" minlength="1">
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Create">
</form>
