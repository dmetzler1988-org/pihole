<?php

declare(strict_types=1);

namespace App\Service;

class ValidateUrl
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
}
