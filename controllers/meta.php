<?php

foreach (glob("../models/*.php") as $filename) {
    include $filename;
}

match ($_SERVER['REQUEST_METHOD']) {
    'POST' => on_post(),
    'GET'  => on_get(),
    'PUT'  => on_put(),
    'DELETE' => on_delete(),
};
