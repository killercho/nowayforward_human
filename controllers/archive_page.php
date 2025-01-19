<?php

function on_post() {
    $WEBSITE_CATEGORY = 'page_url';
    $DOWNLOADS_FOLDER = getenv('ARCHIVES_DIR');
    $website_url = $_POST[$WEBSITE_CATEGORY];
    $currentPage = new DownloadPage($website_url, $DOWNLOADS_FOLDER);
}

class DownloadPage {
    private $zip_location;
    private $zip_name;
    private $page_url;
    private $page_contents;

    function __construct($page_url, $zip_location) {
        $this->zip_location = $zip_location;
        $this->page_url = $page_url;
        list($website_exists, $this->page_url) = $this->does_website_exist($this->page_url);
        if ($website_exists) {
            $this->zip_name = Database\Webpage::create($zip_location, $page_url, 1) . '.zip';
            $this->page_contents = $this->download_file($this->page_url);
            $zip = $this->create_zip_archive();
        } else {
            echo "Website does not exist";
        }
    }

    function set_zip_location($zip_location) {
        $this->zip_location = $zip_location;
    }
    function set_zip_name($zip_name) {
        $this->zip_name = $zip_name;
    }
    function set_page_url($page_url) {
        $this->page_url = $page_url;
    }
    function apply_correct_protocol($url, $protocol) {
        if (str_contains($url, $protocol)) {
            return $url;
        }

        return $protocol . $url;
    }

    function download_file($url) {
        $curl_func = curl_init($url);
        curl_setopt($curl_func, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_func, CURLOPT_FOLLOWLOCATION, true);
        $page_contents = curl_exec($curl_func);
        curl_close($curl_func);
        return $page_contents;
    }

    function does_website_exist($url) {
        // Check if the site exists with https
        $https_url = $this->apply_correct_protocol($url, "https://");
        if ($https_url != $url) {
            $url_headers = @get_headers($https_url);
            if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
                return array(true, $https_url);
            }
        }

        // Check if the site exists with http
        $http_url = $this->apply_correct_protocol($url, "http://");
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

    function resolveUrl($relativeUrl, $baseUrl) {
        // If the url is already absolute return it
        if (parse_url($relativeUrl, PHP_URL_SCHEME)) {
            return $relativeUrl;
        }
        // Otherwise resolve it agains the base url
        return rtrim($baseUrl, '/') . '/' . ltrim($relativeUrl, '/');
    }

    function download_source(&$dom, &$zip, $tagName, $attribute) {
        $links = $dom->getElementsByTagName($tagName);
        foreach($links as $link) {
            $source = $link->getAttribute($attribute);
            if ($source) {
                $sourceUrl = $this->resolveUrl($source, $this->page_url);
                if ($this->is_resource_accessible($sourceUrl)) {
                    $sourceContent = $this->download_file($sourceUrl);
                    if ($sourceContent) {
                        $link->setAttribute($attribute, $sourceUrl);
                        $zip->addFromString(basename($source), $sourceContent);
                    }
                }
            }
        }
    }

    function is_resource_accessible($url) {
        $curl_func = curl_init($url);
        curl_setopt($curl_func, CURLOPT_NOBODY, true); // Gives only the headers
        curl_setopt($curl_func, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_func, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($curl_func);
        $code = curl_getinfo($curl_func, CURLINFO_HTTP_CODE);
        curl_close($curl_func);
        return ($code >= 200 && $code < 400);
    }

    function create_zip_archive() {
        // Creates and returns a zip object resulted from zipping the page that was downloaded
        $zip = new ZipArchive();
        if ($zip->open($this->zip_location . '/' . $this->zip_name, ZipArchive::CREATE) === TRUE) {

            $dom = new DOMDocument();
            @$dom->loadHTML($this->page_contents); // This suppresses warnings for invalid HTML

            $this->download_source($dom, $zip, 'link', 'href');
            $this->download_source($dom, $zip, 'script', 'src');
            $this->download_source($dom, $zip, 'img', 'src');

            $this->page_contents = $dom->saveHTML();
            $zip->addFromString('index.html', $this->page_contents);
            $zip->close();
            echo "Archived {$this->page_url}";
        } else {
            echo "Zip archive could not be open";
        }

        return $zip;
    }
}
