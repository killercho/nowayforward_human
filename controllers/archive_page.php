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
        // Search for all the regexes that fit the *url* pattern where the pattern is the requested url but without the protocol
        $page_url_pattern = $this->getCorrectLinkPattern($page_url);
        $simular_pages = Database\Webpage::getArchivePathsByPattern('%' . $page_url_pattern . '%');
        if ($website_exists) {
            $this->folder_name = Database\Webpage::create($folder_location, $page_url, 1);
            $this->page_contents = $this->downloadFile($this->page_url);
            $this->createArchive($simular_pages);
        } else {
            echo "Website does not exist";
        }
    }

    function getCorrectLinkPattern($page_url) : string {
        $page_url = substr($page_url, strpos($page_url, "//"), strlen($page_url));
        return $page_url;
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
    function applyCorrectProtocol($url, $protocol) : string {
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

    function doesWebsiteExist($url) : array {
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

    function downloadSource(&$dom, $folder_path, $tagName, $attribute, $simular_pages) : void {
        $links = $dom->getElementsByTagName($tagName);
        foreach($links as $link) {
            $source = $link->getAttribute($attribute);
            if ($source) {
                $sourceUrl = $this->resolveUrl($source, $this->page_url);
                if ($this->isResourceAccessible($sourceUrl)) {
                    $sourceContent = $this->downloadFile($sourceUrl);
                    if ($sourceContent) {
                        $found_resource = false;
                        if (count($simular_pages) != 0) {
                            // Page is not unique so check if any other already downloaded resource is
                            // the same as the resource that is needed thus not actually needing to download it
                            foreach($simular_pages as $page) {
                                $resourceName = basename($source);
                                if (!file_exists($this->folder_location . "/" . $page->WID . "/" . $resourceName)) {
                                    continue;
                                }
                                $resourceContents = file_get_contents($this->folder_location . "/" . $page->WID . "/" . $resourceName);
                                if (strlen($resourceContents) == strlen($sourceContent) && md5($resourceContents) == md5($sourceContent)) {
                                    // They are the same resource
                                    // change the link to point to the source of the previous archive instead of downloading a news source
                                    $link->setAttribute($attribute, "../" . $page->WID . "/" . $resourceName);
                                    $found_resource = true;
                                    break;
                                }
                            }
                        }

                        if (!$found_resource) {
                            // Page is unique so there will be no resource that can be cached
                            $link->setAttribute($attribute, './' . basename($source));
                            $file = fopen($folder_path . '/' .  basename($source), "w");
                            fwrite($file, $sourceContent);
                            fclose($file);
                        }
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

    function createArchive($simular_pages) : void {
        // Creates the folder with the correct resources and the main html page in a index.html tag
        $dom = new DOMDocument();
        @$dom->loadHTML($this->page_contents); // This suppresses warnings for invalid HTML

        $folder_path = $this->folder_location . '/' . $this->folder_name;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $this->downloadSource($dom, $folder_path, 'link', 'href', $simular_pages);
        $this->downloadSource($dom, $folder_path, 'script', 'src', $simular_pages);
        $this->downloadSource($dom, $folder_path, 'img', 'src', $simular_pages);

        $this->page_contents = $dom->saveHTML();
        $indexFile = fopen($folder_path . '/index.html', "w");
        fwrite($indexFile, $this->page_contents);
        fclose($indexFile);
        echo "Archived {$this->page_url}";
    }
}
