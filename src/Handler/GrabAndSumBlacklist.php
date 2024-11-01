<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\FileService;
use App\Service\SortContent;
use App\Service\UrlService;

// TODO: append to other restored contents

class GrabAndSumBlacklist
{
    protected array $ignoredFiles = ['README.md', '.DS_Store'];
    protected array $ignoredFolders = ['.', '..'];
    protected array $ignoredLines = ['comments' => '/^\#/'];

    public function __construct()
    {
        $fileService = new FileService($this->ignoredFiles, $this->ignoredFolders, $this->ignoredLines);
        $urlService = new UrlService();

        // Get urls to grab content of blacklist file
        $content = $fileService->getFileContent('./../blacklist/blacklist-updated.txt');

        // TODO: (optional) validate urls for cleanup?

        // Grab content from urls
        $content = $urlService->getContentFromUrls($content);

        // Save content from urls into files (for review, manual checks and backups)
        $fileService->writeContentToFiles($content);

        // Get content of each file once in $content
        $content = $fileService->getMergedFiles('./../blacklist/available');

        // Sort content
        $content = (new SortContent())->getSortedContent($content);

        // TODO: (optional) validate urls for cleanup?
        //$content = (new UrlService())->getValidatedUrlsByCurl($content);

        // Save content into one file
        $fileService->writeContentToFile($content, './../blacklist/mergedAvailableFiles.txt');
    }
}