<?php

$url = $_GET['url'];
$title = $url . ' archive';

function normalizeUrl(string &$url) : void {
    $count_slashes = substr_count($url, "/");
    if (str_ends_with($url, "/index.html")) {
        $url = substr($url, 0, strlen($url) - strlen("/index.html"));
    }
    elseif (str_ends_with($url, "/index")) {
        $url = substr($url, 0, strlen($url) - strlen("/index"));
    }
    elseif (str_ends_with($url, "/")) {
        $url = substr($url, 0, -1);
    }
}

function applyCorrectProtocol($url, $protocol) : string {
    if (str_contains($url, $protocol)) {
        return $url;
    }
    return $protocol . $url;
}

function doesWebsiteExist($url) : array {
    // Check if the site exists with https
    $https_url = applyCorrectProtocol($url, "https://");
    if ($https_url != $url) {
        $url_headers = @get_headers($https_url);
        if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
            return array(true, $https_url);
        }
    }

    // Check if the site exists with http
    $http_url = applyCorrectProtocol($url, "http://");
    if ($http_url != $url) {
        $url_headers = @get_headers($http_url);
        if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
            return array(true, $http_url);
        }
    }

    // Check if the site exists as is
    // Will take effect when the user has entered the https/http protocol with the site
    $url_headers = @get_headers($url);
    if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
        return array(true, $url);
    }

    return array(false, $url);
}
