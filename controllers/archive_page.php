<?php

function on_post() {
    $WEBSITE_CATEGORY = 'page_url';
    $DOWNLOADS_FOLDER = getenv('ARCHIVES_DIR');
    $website_url = $_POST[$WEBSITE_CATEGORY];
    $currentPage = new DownloadPage($website_url, $DOWNLOADS_FOLDER);
}

class DownloadPage {
    private $folder_location;
    private $folder_name;
    private $page_url;
    private $page_contents;

    function __construct($page_url, $folder_location) {
        $this->folder_location = $folder_location;
        $this->page_url = $page_url;
        list($website_exists, $this->page_url) = $this->doesWebsiteExist($this->page_url);
        // TODO: Make a clause for whether the same site was already archived at least once
        // This should happen with a request to the database
        // If such site exists then when downloading the resources check whether some of the resources already exist in the
        // old archive
        // If they do dont download them (or rather delete and make the pointers point to the correct archive folder
        if ($website_exists) {
            $this->folder_name = Database\Webpage::create($folder_location, $page_url, 1);
            $this->page_contents = $this->downloadFile($this->page_url);
            $this->createArchive();
        } else {
            echo "Website does not exist";
        }
    }

    function setFolderLocation($folder_location) : void {
        $this->folder_location = $folder_location;
    }
    function setFolderName($folder_name) : void {
        $this->folder_name = $folder_name;
    }
    function setPageUrl($page_url) : void {
        $this->page_url = $page_url;
    }
    function applyCorrectProtocol($url, $protocol) : void {
        if (str_contains($url, $protocol)) {
            return $url;
        }

        return $protocol . $url;
    }

    function downloadFile($url) : string {
        $curl_func = curl_init($url);
        curl_setopt($curl_func, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_func, CURLOPT_FOLLOWLOCATION, true);
        $page_contents = curl_exec($curl_func);
        curl_close($curl_func);
        return $page_contents;
    }

    function doesWebsiteExist($url) : array(bool, string) {
        // Check if the site exists with https
        $https_url = $this->applyCorrectProtocol($url, "https://");
        if ($https_url != $url) {
            $url_headers = @get_headers($https_url);
            if ($url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found') {
                return array(true, $https_url);
            }
        }

        // Check if the site exists with http
        $http_url = $this->applyCorrectProtocol($url, "http://");
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

    function resolveUrl($relativeUrl, $baseUrl) : string {
        // If the url is already absolute return it
        if (parse_url($relativeUrl, PHP_URL_SCHEME)) {
            return $relativeUrl;
        }
        // Otherwise resolve it agains the base url
        return rtrim($baseUrl, '/') . '/' . ltrim($relativeUrl, '/');
    }

    function downloadSource(&$dom, $folder_path, $tagName, $attribute) : void {
        $links = $dom->getElementsByTagName($tagName);
        foreach($links as $link) {
            $source = $link->getAttribute($attribute);
            if ($source) {
                $sourceUrl = $this->resolveUrl($source, $this->page_url);
                if ($this->isResourceAccessible($sourceUrl)) {
                    $sourceContent = $this->downloadFile($sourceUrl);
                    if ($sourceContent) {
                        $link->setAttribute($attribute, './' . basename($source));
                        $file = fopen($folder_path . '/' .  basename($source), "w");
                        fwrite($file, $sourceContent);
                        fclose($file);
                    }
                }
            }
        }
    }

    function isResourceAccessible($url) : bool {
        $curl_func = curl_init($url);
        curl_setopt($curl_func, CURLOPT_NOBODY, true); // Gives only the headers
        curl_setopt($curl_func, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_func, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($curl_func);
        $code = curl_getinfo($curl_func, CURLINFO_HTTP_CODE);
        curl_close($curl_func);
        return ($code >= 200 && $code < 400);
    }

    function createArchive() : void {
        // Creates the folder with the correct resources and the main html page in a index.html tag
        $dom = new DOMDocument();
        @$dom->loadHTML($this->page_contents); // This suppresses warnings for invalid HTML

        $folder_path = $this->folder_location . '/' . $this->folder_name;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $this->downloadSource($dom, $folder_path, 'link', 'href');
        $this->downloadSource($dom, $folder_path, 'script', 'src');
        $this->downloadSource($dom, $folder_path, 'img', 'src');

        $this->page_contents = $dom->saveHTML();
        $indexFile = fopen($folder_path . '/index.html', "w");
        fwrite($indexFile, $this->page_contents);
        fclose($indexFile);
        echo "Archived {$this->page_url}";
    }
}
