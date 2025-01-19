<?php

function on_post() {
    echo "Id: " . Database\User::create($_POST["Username"], "", "User");
}
