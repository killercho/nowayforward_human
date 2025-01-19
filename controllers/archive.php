<?php

function on_get() {
    global $page;
    try {
        $page = Database\Webpage::fromDB($_GET["page_url"]);
        $page->incrementVisits();
    }
    catch(Exception $e) {}
}
