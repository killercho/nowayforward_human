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

    <h2>Delete</h2>

    <form action="/user/delete" method="GET" class="font-115">
        <input type="text" name="username" placeholder="Username">
        <input type="submit" value="Delete">
    </form>

    <h2>Archive queue</h2>

    <button id="manual-start">Start worker manually</button>
    <script type="text/javascript">
        document.getElementById('manual-start').onclick = function() {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState < 4) return;

                console.log(request.responseText);
            }
            request.open("POST", "/archive/create", true);
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.withCredentials = true;
            request.send('async=true&url=localhost&manual=true');
        }
    </script>

<?php else: ?>
    <h2>Permission denied, you're not an admin!</h2>

<?php endif; ?>
