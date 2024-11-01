<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\FileService;
use App\Service\SortContent;
use App\Service\UrlService;

class CleanupRestoredFiles
{
    protected array $ignoredFiles = ['README.md', '.DS_Store'];
    protected array $ignoredFolders = ['.', '..'];
    protected array $ignoredLines = ['comments' => '/^\#/'];
    protected FileService $fileService;

    public function __construct()
    {
        $this->fileService = new FileService($this->ignoredFiles, $this->ignoredFolders, $this->ignoredLines);

        // Get content of each file once in $content
        $content = $this->fileService->getMergedFiles('./../blacklist/restored');

        // Sort content
        $content = (new SortContent())->getSortedContent($content);

        // TODO: (optional) validate urls for cleanup?
        //$content = (new UrlService())->getValidatedUrlsByCurl($content);

        // Save content into one file
        $this->fileService->writeContentToFile($content, './../blacklist/mergedRestoreFiles.txt');
    }
}

