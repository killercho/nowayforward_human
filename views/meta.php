<?php

include "../header.php";

function end_page() {
    include "../footer.php";
}

foreach (glob("../../models/*.php") as $filename) {
    include $filename;
}

function runController(string $name) {
    include "../../controllers/$name.php";
    include '../../controllers/meta.php';
}
