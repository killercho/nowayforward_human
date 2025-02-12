<?php
    $user = require_login();
?>

<?php if ($user->Role === 'Admin'): ?>
    <h1>Global settings</h1>

    <h2>Change role</h2>

    <form action="#" method="POST" class="font-115 flex-row">
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

    <form action="/user/delete" method="GET" class="font-115 flex-row">
        <input type="text" name="username" placeholder="Username">
        <input type="submit" value="Delete">
    </form>

    <h2>Archive queue</h2>

    <section>
    <button id="manual-start">Start worker manually</button>
    <span id="start-msg"></span>
    <script type="text/javascript">
        document.getElementById('manual-start').onclick = function() {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState < 4) return;

                document.getElementById('start-msg').innerText = 'Response: ' + request.responseText;
            }
            request.open("POST", "/archive/create", true);
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.withCredentials = true;
            request.send('async=true&url=localhost&manual=true');
            document.getElementById('start-msg').innerText = 'Sent! If you see this for a long time, worker is archiving.';
        }
    </script>
    </section>

    <p></p>

    <section>
    <button id="clear">Clear worker queue</button>
    <span id="clear-msg"></span>
    <script type="text/javascript">
        document.getElementById('clear').onclick = function() {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState < 4) return;

                if (request.status === 200) {
                    document.getElementById('clear-msg').innerText = 'Done!';
                }
                else {
                    document.getElementById('clear-msg').innerText = 'Error: ' + request.responseText;
                }
            }
            request.open("POST", "/archive/clear_queue.php", true);
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.withCredentials = true;
            request.send(null);
        }
    </script>
    </section>

<?php else: ?>
    <h2>Permission denied, you're not an admin!</h2>

<?php endif; ?>
