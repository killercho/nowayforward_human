<?php
    $user = require_login();
?>

<?php if ($user->Role === 'Admin'): ?>
    <h2>Change role</h2>

    <form action="#" method="POST" class="font-115">
        <input type="hidden" name="method" value="PATCH">
        <?php if (isset($role_status)): ?>
            <?php if ($role_status !== ""): ?>
                <p class="item error"><span>
                    <strong>Error:</strong> <?= $role_status ?>
                </span></p>
            <?php else: ?>
                <p class="item success">
                    Success!
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <input type="hidden" name="type" value="role">
        <input type="text" name="username" placeholder="Username">
        <select name="role" required>
            <option value="User">User</option>
            <option value="Admin">Admin</option>
        </select>
        <input type="submit" value="Modify">
    </form>

<?php else: ?>
    <h2>Permission denied, you're not an admin!</h2>

<?php endif; ?>
