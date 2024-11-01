<?php

declare(strict_types=1);

namespace App\Service;

class FileService
{
    public function __construct(
        protected array $ignoredFiles,
        protected array $ignoredFolders,
        protected array $ignoredLines
    ) {
    }

    public function writeContentToFile(array $content, string $filePath = './../mergedFile.txt')
    {
        $fileContent = implode(PHP_EOL, $content);

        $mergedFile = fopen($filePath, 'w') or die("Can't create file");
        fwrite($mergedFile, $fileContent . PHP_EOL);
        fclose($mergedFile);
    }

    public function writeContentToFiles(array $content, string $filePath = './../blacklist/available/'): void
    {
        foreach ($content as $filename => $fileContent) {
            $parsedUrl = parse_url($filename);
            $filename = str_replace('/', '_', $parsedUrl['host'] . $parsedUrl['path']) . '.txt';

            //$fileContent = implode(PHP_EOL, $fileContent);

            $mergedFile = fopen($filePath . $filename, 'w') or die("Can't create file");
            fwrite($mergedFile, $fileContent . PHP_EOL);
            fclose($mergedFile);
        }
    }

    public function readContentFromFile(string $filePath = 'mergedFile.txt'): ?array
    {
        $fileContent = file_get_contents($filePath) or die("Can't open file");
        $fileContent = array_filter(explode(PHP_EOL, $fileContent));

        return $fileContent;
    }

    public function recursiveScanDir($dir, &$results = []): array
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            if (in_array($value, $this->ignoredFiles) || in_array($value, $this->ignoredFolders)) {
                continue;
            }

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else {
                $this->recursiveScanDir($path, $results);
            }
        }

        return $results;
    }

    public function getFilesContents(array $files): array
    {
        $filesContent = [];
        foreach ($files as $file) {
            if (in_array($file, $this->ignoredFiles) || in_array($file, $this->ignoredFolders)) {
                continue;
            }

            $content = [];
            $fileContent = file($file);
            foreach ($fileContent as $key => $value) {
                $content[$key] = rtrim($value);
            }

            $filesContent[$file] = $content;
        }

        return $filesContent;
    }

    public function getFileContent(string $file): ?array
    {
        $fileContent = [];
        $fileContent = file($file);
        foreach ($fileContent as $key => $value) {
            $fileContent[$key] = rtrim($value);
        }

        return $fileContent;
    }

    public function getMergedFiles(string $scandir): array
    {
        $files = $this->recursiveScanDir($scandir);
        $filesContent = $this->getFilesContents($files);

        $merged = [];
        foreach ($filesContent as $fileKey => $fileValues) {
            foreach ($fileValues as $line) {
                $line = trim($line);
                // Ignore comments and empty lines.
                if (
                    empty($line) ||
                    preg_match($this->ignoredLines['comments'], $line)
                ) {
                    continue;
                }

                // Remove some parts of some beginnings on lines like "127.0.0.1 www.google.com"
                $line = preg_replace('/^127\.0\.0\.1[ \t\f\r]|0\.0\.0\.0[ \t\f\r]|0[ \t\f\r]/', '', $line);

                array_push($merged, $line);
            }
        }

        return array_unique($merged);
    }
}
