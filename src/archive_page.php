<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP answer to the request</title>
</head>
<body>
<p>
<?php
    function apply_correct_protocol($url, $protocol) {
        if (str_contains($url, $protocol)) {
            return $url;
        }

        return $protocol . $url;
    }

    function does_website_exist($url) {
        $result = false;

        // Check if the site exists with https
        $https_url = apply_correct_protocol($url, "https://");
        if ($https_url != $url) {
            $url_headers = @get_headers($https_url);
            $result |= $url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found';
        }

        // Check if the site exists with http
        $http_url = apply_correct_protocol($url, "http://");
        if ($http_url != $url) {
            $url_headers = @get_headers($http_url);
            $result |= $url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found';
        }

        // Check if the site exists as is
        // Will take effect when the user has entered the https/http protocol with the site
        $url_headers = @get_headers($url);
        $result |= $url_headers && $url_headers[0] != 'HTTP/1.1 404 Not Found';

        return $result;
    }

    $WEBSITE_CATEGORY = 'page_url';
    $DATABASE_NAME = 'db';
    $TABLE_NAME = 'users';
    $website_url = $_POST[$WEBSITE_CATEGORY];
    $website_exists = does_website_exist($website_url) ? "true" : "false";
    echo "Website exists: $website_exists" . "<br/>";

    try {
        $db = new PDO("mysql:host=localhost;dbname=$DATABASE_NAME", $user, $password);
        echo $db->query("DESCRIBE $TABLE_NAME");
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>
</p>
</body>
</html>
