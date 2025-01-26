<?php
namespace Controller;
use Database;
use DOMDocument;
use Exception;

function on_post() {
    global $TOKEN;

    $WEBSITE_CATEGORY = 'page_url';
    $DOWNLOADS_FOLDER = getenv('ARCHIVES_DIR');
    $website_url = $_POST[$WEBSITE_CATEGORY];
    $uid = 1;
    if ($TOKEN !== "") {
        try {
            $uid = Database\Cookie::fromDB($TOKEN)->UID;
        }
        catch (Exception $e) {}
    }
    $currentPage = new DownloadPage($website_url, $DOWNLOADS_FOLDER, $uid);
}

class DownloadPage {
    private $folder_location;
    private $folder_name;
    private $page_url;
    private $page_contents;
    private $favicon_path;
    private $page_title;

    function __construct($page_url, $folder_location, $requester_uid) {
        $this->folder_location = $folder_location;
        $this->page_url = $page_url;
        $this->normalizeUrl($this->page_url);
        list($website_exists, $this->page_url) = $this->doesWebsiteExist($this->page_url);
        // Search for all the regexes that fit the *url* pattern where the pattern is the requested url but without the protocol
        $page_url_pattern = $this->getCorrectLinkPattern($this->page_url);
        $simular_pages = Database\Webpage::getArchivePathsByPattern('%' . $page_url_pattern . '%');
        if ($website_exists) {
            $this->folder_name = Database\Webpage::getPagesCount() + 1;
            $this->page_contents = $this->downloadFile($this->page_url);
            $this->createArchive($simular_pages);
            if (!$this->favicon_path) {
                // No favicons were found in the normal links
                // Fallback and try to download them from the server directly
                $this->tryDownloadFavicon();
            }
            Database\Webpage::create($folder_location, $this->page_url, $requester_uid, $this->favicon_path, $this->page_title);
        } else {
            echo "Website does not exist";
        }
    }

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

    private function debugPrintToConsole($data) : void{
         $output = $data;
         if (is_array($output))
             $output = implode(',', $output);

         echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    function tryDownloadFavicon() : void {
        // Tries to download an icon from the server directly
        // The tried names are favicon.png/ico/jpeg/jpg/svg

        foreach(["png", "ico", "jpeg", "jpg", "svg"] as $ending) {
            $currentName = "/favicon." . $ending;
            $currentLink = $this->page_url . $currentName;
            if ($this->downloadFavicon($currentLink, $currentName)) {
                break;
            }
        }
    }

    function downloadFavicon(string $currentLink, string $currentName) : bool {
        if ($this->isResourceAccessible($currentLink)) {
            $sourceContent = $this->downloadFile($currentLink);
            if ($sourceContent) {
                $resourceName = basename($currentName);
                $folder_path = $this->folder_location . '/' . $this->folder_name;
                $file = fopen($folder_path . '/' .  $resourceName, "w");
                if ($file){
                    fwrite($file, $sourceContent);
                    fclose($file);
                    $this->favicon_path = $this->folder_name . $currentName;
                    return true;
                }
            }
        }
        return false;
    }

    function getCorrectLinkPattern($page_url) : string {
        // NOTE: Offset by 2 because of the '//' of the protocol
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
        // Get only the domain with the protocol
        $pattern = '/((^.*\/\/|.{0,0})[a-z0-9A-Z\.]+)(\/\w+|$)/';
        $actualUrl = $baseUrl;
        if (preg_match($pattern, $baseUrl, $matches)) {
            $actualUrl = $matches[1];
        }
        return rtrim($actualUrl, '/') . '/' . ltrim($relativeUrl, '/');
    }

    function handleCssUrls(&$content) : void {
        if (preg_match_all('/url\((.*)\)/', $content, $matches, PREG_PATTERN_ORDER) > 0) {
            $urls = $matches[1];

            foreach ($urls as $url) {
                $original_url = $url;
                $url = ltrim($url, "'()");
                $url = rtrim($url, "'()");
                $url = substr($url, 0, strpos($url, "'"));

                $url = $this->resolveUrl($url, $this->page_url);

                if ($this->isResourceAccessible($url)) {
                    // Get the file name and local path
                    $file_name = basename($url);
                    $file_path = './' . $file_name;
                    $folder_path = $this->folder_location . '/' . $this->folder_name;
                    $urlContents = $this->downloadFile($url);
                    if ($urlContents) {
                        // Save the resource locally
                        $file = fopen($folder_path . '/' .  $file_name, "w");
                        if ($file){
                            fwrite($file, $urlContents);
                            fclose($file);
                        }
                        // Replace the URL in the CSS content
                        $content = str_replace($original_url, "'" . $file_path . "'", $content);
                    }
                }
            }
        }
    }

    function handleJsImports(&$content) : void {
        if (preg_match_all("/import .*'(.*)'/", $content, $matches, PREG_PATTERN_ORDER) > 0) {
            $urls = $matches[1];

            foreach ($urls as $url) {
                $original_url = $url;
                $url = ltrim($url, "./");
                $url = rtrim($url, "./");

                $url = $this->resolveUrl($url, $this->page_url);

                if ($this->isResourceAccessible($url)) {
                    // Get the file name and local path
                    $file_name = basename($url);
                    $file_path = './' . $file_name;
                    $folder_path = $this->folder_location . '/' . $this->folder_name;
                    $urlContents = $this->downloadFile($url);
                    if ($urlContents) {
                        // Save the resource locally
                        $file = fopen($folder_path . '/' .  $file_name, "w");
                        if ($file){
                            fwrite($file, $urlContents);
                            fclose($file);
                        }
                        // Replace the URL in the CSS content
                        $content = str_replace($original_url, "'" . $file_path . "'", $content);
                    }
                }
            }
        }
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
                        if ($tagName == "link") {
                            // The resource is a css resource most likely
                            // Go trough the resource, download the urls and replace them with their local path
                            $this->handleCssUrls($sourceContent);
                        } elseif ($tagName == "script") {
                            // The resource is a script resource most likely
                            // Go trough the resource, download the imports and replace them with their local path
                            $this->handleJsImports($sourceContent);
                        }
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
                                    if ($tagName == "link") {
                                        $faviconTry = $link->getAttribute("rel");
                                        if ($faviconTry && ($faviconTry == "icon" || $faviconTry == "icon shortcut")) {
                                            $this->favicon_path = $page->WID . "/" . $resourceName;
                                        }
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
                            if ($tagName == "link") {
                                $faviconTry = $link->getAttribute("rel");
                                if ($faviconTry && ($faviconTry == "icon" || $faviconTry == "icon shortcut")) {
                                    $this->favicon_path = $this->folder_name . "/" . $resourceName;
                                }
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
            $link = $this->resolveUrl($link, $this->page_url);
            $page_url_pattern = $this->getCorrectLinkPattern($link);
            $correct_results = Database\Webpage::getArchivePathsByPattern('%' . $page_url_pattern . '%');

            if (count($correct_results) != 0) {
                // If there are any links that are the same as the urls make the $dom attribute point
                // to the latest version of that page
                $tag->setAttribute($attribute, "/archives/" . $correct_results[0]->WID . "/index.html");
            } else {
                // If there are no pages that are like that url point to the landing page of the site
                // that says that this page was not yet archived
                $tag->setAttribute($attribute, "/archive/?url=" . $link);
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

    function updatePageTitle(&$dom) {
        $titles = $dom->getElementsByTagName("title");
        if ($titles->length > 0) {
            $this->page_title = $titles->item(0)->textContent;
        }
    }

    function getArchiveTop() : array {
        $content = '<?php
            require_once "../../models/database.php";
            require_once "../../models/webpage.php";
            require_once "../../models/user.php";

            function debugPrintToConsole($data) : void {
                 $output = $data;
                 if (is_array($output))
                     $output = implode(",", $output);

                 echo "<script>console.log(\"Debug Objects: " . $output . "\" );</script>";
            }
            debugPrintToConsole("This is necessary for some reason, without it the content is not actually shown!");

            $currentPageId = basename(__DIR__);
            $currentPage = Database\Webpage::getPageById($currentPageId);
            $requesterUsername = Database\User::fromDBuid($currentPage->RequesterUID);

            $previousPageId = Database\Webpage::getPreviousPageId($currentPage->URL, $currentPage->Date);
            $nextPageId = Database\Webpage::getNextPageId($currentPage->URL, $currentPage->Date);

            echo "<div class=\"navbar\">";
            echo "<div class=\"navbar-info\">";
            echo "<span>Title: $currentPage->Title</span>";
            echo "<span>Url: $currentPage->URL</span>";
            echo "<span>Date of archival: $currentPage->Date</span>";
            echo "<span>Visits: $currentPage->Visits</span>";
            echo "<span>Requested by: $requesterUsername->Username</span>";

            echo "<div class=\"navbar-links\">";
            if ($previousPageId != 0) {
                echo "<a href=\"../$previousPageId/index.php\">Previous version</a>";
            }
            if ($nextPageId != 0) {
                echo "<a href=\"../$nextPageId/index.php\">Next version</a>";
            }
            echo "</div>";
            echo "</div>";
        ?>';

        $style = '
            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #343a40;
                color: #ffffff;
                padding: 10px;
                border-bottom: 1px solid #ccc;
                width: 100%;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1000;
            }

            .navbar-info {
                display: flex;
                justify-content: center;
                flex-grow: 1;
            }

            .navbar-info span {
                margin-right: 15px;
            }

            .navbar-links {
                display: flex;
                gap: 20px;
            }

            .navbar a {
                text-decoration: none;
                color: #007bff;
            }

            .navbar a:hover {
                text-decoration: underline;
                color: #66b3ff;
            }

            /* Add some margin to the body to prevent content from being hidden behind the navbar */
            body {
                margin-top: 60px;
            }
        ';
        return array($content, $style);
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
        $this->downloadSource($dom, $folder_path, 'frame', 'src', $simular_pages);

        $this->updatePageTitle($dom);

        $this->changeHyperlinkToLocal($dom, 'a', 'href');

        // Add the header for the archives
        list($archive_top, $archive_top_style) = $this->getArchiveTop();
        $phpTag = $dom->createElement('script', $archive_top);
        $phpTag->setAttribute('type', 'text/php'); // Set the type to PHP
        $body = $dom->getElementsByTagName('body')->item(0);
        $body->appendChild($phpTag);

        $styleTag = $dom->createElement('style', $archive_top_style);
        $head = $dom->getElementsByTagName('head')->item(0);
        $head->appendChild($styleTag);

        $this->page_contents = $dom->saveHTML();
        $indexFile = fopen($folder_path . '/index.php', "a");
        fwrite($indexFile, $this->page_contents);
        fclose($indexFile);
    }
}

function applyCorrectProtocol($url, $protocol) : string {
    if (str_contains($url, $protocol)) {
        return $url;
    }
    return $protocol . $url;
}

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
