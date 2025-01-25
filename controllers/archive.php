<?php

function on_get() {
    global $page;
    try {
        $page = Database\Webpage::fromDB($_GET["page_url"]);
        $page->incrementVisits();
    }
    catch(Exception $e) {}
}

function applyCorrectProtocol($url, $protocol) : string {
    if (str_contains($url, $protocol)) {
        return $url;
    }
    return $protocol . $url;
}

function doesWebsiteExist($url) : bool {
    // Check if the site exists with https
    $https_url = applyCorrectProtocol($url, "https://");
    if ($https_url != $url) {
        $url_headers = @get_headers($https_url);
        if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
            return true;
        }
    }

    // Check if the site exists with http
    $http_url = applyCorrectProtocol($url, "http://");
    if ($http_url != $url) {
        $url_headers = @get_headers($http_url);
        if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
            return true;
        }
    }

    // Check if the site exists as is
    // Will take effect when the user has entered the https/http protocol with the site
    $url_headers = @get_headers($url);
    if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
        return true;
    }

    return false;
}
