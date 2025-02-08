<?php

require_once "../../models/database.php";
require_once "../../models/webpage.php";

$page = Database\Webpage::fromDBwid($_POST['wid']);
$page->incrementVisits();
