<?php

declare(strict_types=1);

namespace App\Service;

class UrlService
{
    public function __construct()
    {
    }

    public function getValidContent(array $content): ?array
    {
        $validContent = $content;
        foreach ($validContent as $key => $url) {
            // TODO: catch warnings ("unable to connect") and save it to log file
            $open = fSockOpen($url, 80, timeout: 2);
            if (!$open) {
                unset($validContent[$key]);
            }
        }

        return $validContent;
    }

    public function getValidatedUrlsByHeaders(array $content): ?array
    {
        $validContent = $content;
        foreach ($validContent as $key => $url) {
            // TODO: catch warnings ("unable to connect") and save it to log file

            // Get the headers of the URL - not working correctly with redirects
            $headers = @get_headers('http://' . $url);

            if ($headers === false || strpos($headers[0], '200') === false) {
                unset($validContent[$key]);
            }
        }

        return $validContent;
    }

    public function getValidatedUrlsByPing(array $content): ?array
    {
        $validContent = $content;
        foreach ($validContent as $key => $url) {
            // TODO: catch warnings ("unable to connect") and save it to log file

            exec("ping -c 1 $url", $output, $result);

            // Check the result code; 0 means success
            if ($result !== 0) {
                echo "Ping failed for $url\n";
                unset($validContent[$key]);
            }
        }

        return $validContent;
    }

    public function getValidatedUrlsByCurl(array $content): ?array
    {
        $validContent = $content;
        foreach ($validContent as $key => $url) {
            // TODO: catch warnings ("unable to connect") and save it to log file

            // Initialize cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true); // We don't need the body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a timeout for the request
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

            // Execute the request
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP response code

            // Close the cURL session
            curl_close($ch);

            // Check the result code
            if ($httpCode !== 200) {
                echo "Ping failed for $url\n";
                unset($validContent[$key]);
            }
        }

        return $validContent;
    }

    public function getContentFromUrls(array $urls): ?array
    {
        $contents = [];
        foreach ($urls as $url) {
            // Initialize cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false); // We don't need the header
            curl_setopt($ch, CURLOPT_ENCODING, ''); // Handle all encodings
            curl_setopt($ch, CURLOPT_AUTOREFERER, true); // Set referer on redirect
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set a timeout for the request
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // Stop after 5 redirects

            // Execute the request
            $content = curl_exec($ch);
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            $header = curl_getinfo($ch);

            // Close the cURL session
            curl_close($ch);

            $header['errno'] = $err;
            $header['errmsg'] = $errmsg;
            $header['content'] = $content;

            /*if ((strpos($header['content_type'], 'text/plain') === false)
                && (strpos($header['content_type'], 'plain/text') === false)
            ) {
                echo "$url has content type: " . $header['content_type'] . "\n";
                continue;
            }*/

            if ($header['http_code'] !== 200) {
                echo "$url not found http code: " . $header['http_code'] . "\n";
                continue;
            }

            if (empty($content)) {
                echo "$url has no content.\n";
                continue;
            }

            $contents[$url] = $content;
        }

        return $contents;
    }
}
