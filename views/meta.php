<?php

include_once "../header.php";

function end_page() {
    include_once "../footer.php";
}

include_once "../../models/database.php";
foreach (glob("../../models/*.php") as $filename) {
    include_once $filename;
}

function runController(string $name) {
    include_once "../../controllers/$name.php";
    include_once '../../controllers/meta.php';
}
