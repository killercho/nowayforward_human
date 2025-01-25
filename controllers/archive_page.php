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
    private $favicon_path;

    private function debugPrintToConsole($data) : void{
         $output = $data;
         if (is_array($output))
             $output = implode(',', $output);

         echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    function __construct($page_url, $folder_location) {
        $this->folder_location = $folder_location;
        $this->page_url = $page_url;
        list($website_exists, $this->page_url) = $this->doesWebsiteExist($this->page_url);
        // Search for all the regexes that fit the *url* pattern where the pattern is the requested url but without the protocol
        $page_url_pattern = $this->getCorrectLinkPattern($page_url);
        $simular_pages = Database\Webpage::getArchivePathsByPattern('%' . $page_url_pattern . '%');
        if ($website_exists) {
            $this->folder_name = Database\Webpage::getPagesCount() + 1;
            $this->page_contents = $this->downloadFile($this->page_url);
            $this->createArchive($simular_pages);
            Database\Webpage::create($folder_location, $page_url, 1, $this->favicon_path);
        } else {
            echo "Website does not exist";
        }
    }

    function getCorrectLinkPattern($page_url) : string {
        // NOTE: Offset by 2 because of the '//' of the protocol
        $page_url = substr($page_url, strpos($page_url, "//") + 2, strlen($page_url));
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
        curl_setopt($curl_func, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; compatible; pageburst) Chrome/131.0.6778.204 Safari/537.36");
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
                                    if (str_contains($resourceName, "favicon")) {
                                        $this->favicon_path = $page->WID . "/" . $resourceName;
                                    }
                                    break;
                                }
                            }
                        }

                        if (!$found_resource) {
                            // Page is unique so there will be no resource that can be cached
                            $resourceName = basename($source);
                            $link->setAttribute($attribute, './' . $resourceName);
                            $file = fopen($folder_path . '/' .  $resourceName, "w");
                            if ($file){
                                fwrite($file, $sourceContent);
                                fclose($file);
                            }
                            if (str_contains($resourceName, "favicon")) {
                                $this->favicon_path = $this->folder_name . "/" . $resourceName;
                            }
                        }
                    }
                }
            }
        }
    }

    // Changes the hyperlinks in the site to ones that are local for the site
    // or to the landing page when a page is not archived if the hyperlink of the
    // other page is not archived
    function changeHyperlinkToLocal(&$dom, $tagName, $attribute) : void {
        $tags = $dom->getElementsByTagName($tagName);
        foreach($tags as $tag) {
            $link = $tag->getAttribute($attribute);
            // Make a request to the db and check if any URLs like the 'link'
            // exist in it and are presently donwloaded
            //$link_url = $this->resolveUrl($link);
            $page_url_pattern = $this->getCorrectLinkPattern($link);
            // TODO: The link should depend on whether there is a domain in the front or not
            $correct_results = Database\Webpage::getArchivePathsByPattern('%' . $page_url_pattern . '%');

            if (count($correct_results) != 0) {
                // If there are any links that are the same as the urls make the $dom attribute point
                // to the latest version of that page
                $tag->setAttribute($attribute, "../" . $correct_results[0]->WID . "/index.html");
            } else {
                // If there are no pages that are like that url point to the landing page of the site
                // that says that this page was not yet archived
                $tag->setAttribute($attribute, "../../archive/index.php?page_url=" . $this->baseToFullUrlForGet($this->page_url, $link));
            }
        }
    }

    function baseToFullUrlForGet($url, $base) : string {
        $replaced = rtrim($url, '/') . '/' . ltrim($base, '/');
        $replaced = str_replace('/', '%2F', $replaced);
        $replaced = str_replace(':', '%3A', $replaced);
        return $replaced;
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
        $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'; // Ensures the encoding is UTF-8
        @$dom->loadHTML($contentType . $this->page_contents); // This suppresses warnings for invalid HTML

        $folder_path = $this->folder_location . '/' . $this->folder_name;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $this->downloadSource($dom, $folder_path, 'link', 'href', $simular_pages);
        $this->downloadSource($dom, $folder_path, 'script', 'src', $simular_pages);
        $this->downloadSource($dom, $folder_path, 'img', 'src', $simular_pages);

        $this->changeHyperlinkToLocal($dom, 'a', 'href');

        $this->page_contents = $dom->saveHTML();
        $indexFile = fopen($folder_path . '/index.html', "w");
        fwrite($indexFile, $this->page_contents);
        fclose($indexFile);
        echo "Archived {$this->page_url}";
    }
}
