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
        list($website_exists, $this->page_url) = $this->does_website_exist($this->page_url);
        if ($website_exists) {
            $this->folder_name = Database\Webpage::create($folder_location, $page_url, 1);
            $this->page_contents = $this->download_file($this->page_url);
            $this->create_archive();
        } else {
            echo "Website does not exist";
        }
    }

    function set_folder_location($folder_location) {
        $this->folder_location = $folder_location;
    }
    function set_folder_name($folder_name) {
        $this->folder_name = $folder_name;
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

    function download_source(&$dom, $folder_path, $tagName, $attribute) {
        $links = $dom->getElementsByTagName($tagName);
        foreach($links as $link) {
            $source = $link->getAttribute($attribute);
            if ($source) {
                $sourceUrl = $this->resolveUrl($source, $this->page_url);
                if ($this->is_resource_accessible($sourceUrl)) {
                    $sourceContent = $this->download_file($sourceUrl);
                    if ($sourceContent) {
                        $link->setAttribute($attribute, $folder_path . '/' . basename($source));
                        // NOTE: This might need to be the basename instead of the sourceUrl
                        $file = fopen($folder_path . '/' .  basename($source), "w");
                        fwrite($file, $sourceContent);
                        fclose($file);
                        //$zip->addFromString(basename($source), $sourceContent);
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

    function create_archive() {
        // Creates the folder with the correct resources and the main html page in a index.html tag
        $dom = new DOMDocument();
        @$dom->loadHTML($this->page_contents); // This suppresses warnings for invalid HTML

        $folder_path = $this->folder_location . '/' . $this->folder_name;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $this->download_source($dom, $folder_path, 'link', 'href');
        $this->download_source($dom, $folder_path, 'script', 'src');
        $this->download_source($dom, $folder_path, 'img', 'src');

        $this->page_contents = $dom->saveHTML();
        $indexFile = fopen($folder_path . '/index.html', "w");
        fwrite($indexFile, $this->page_contents);
        fclose($indexFile);
        echo "Archived {$this->page_url}";
    }
}
