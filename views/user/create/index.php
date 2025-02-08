<h1>Register</h1>

<hr class="new-section"/>

<form action="#" method="POST" class="font-115 flex-col-centered max-width-20 center-margin">
    <?php if (isset($user_status)): ?>
        <?php if ($user_status !== ""): ?>
            <p class="item error"><span>
                <strong>Error:</strong> <?= $user_status ?>
            </span></p>
        <?php else: ?>
            <p class="item success">
                Success!
            </p>
        <?php endif; ?>
    <?php endif; ?>

    <input id="name" type="text" name="username" placeholder="Username" minlength="1" pattern="[A-Za-z][A-Za-z_0-9]*" required>
    <input id="pass1" type="password" name="password" placeholder="Password" minlength="4" required>
    <input id="pass2" type="password" name="password" placeholder="Confirm password" minlength="4" required>
    <input type="submit" value="Register">
</form>

<script type="text/javascript">
const name = document.getElementById('name');
name.onkeyup = function () {
    if (!name.reportValidity()) {
        name.setCustomValidity('Username must start with a character and must be composed of characters, digits and underscores!');
    }
    else {
        name.setCustomValidity('');
    }
}

const pass1 = document.getElementById('pass1');
const pass2 = document.getElementById('pass2');

function validatePasswords() {
    if (pass1.value !== pass2.value) {
        pass2.setCustomValidity('Passwords do not match!');
    }
    else {
        pass2.setCustomValidity('');
    }
}

pass1.onchange = validatePasswords;
pass2.onkeyup = validatePasswords;
</script>
