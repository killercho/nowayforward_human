<?php

function on_post() {
    Database\User::create($_POST["Username"], "", "User");
}
