<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\FileHandler;
use App\Service\SortContent;
use App\Service\ValidateUrl;

// TODO: (optional) execute validator on restored files once and minimize the contents to working urls only (because its very slow)

class CleanupRestoredFiles
{
    protected array $ignoredFiles = ['README.md', '.DS_Store', 'blocklist.txt'];
    protected array $ignoredFolders = ['.', '..'];
    protected array $ignoredLines = ['comments' => '/^\#/'];
    protected FileHandler $fileHandler;

    public function __construct()
    {
        $this->fileHandler = new FileHandler($this->ignoredFiles, $this->ignoredFolders, $this->ignoredLines);
        /*$content = [
            "vd.emp.prd.s3.amazonaws.com",
            "vdterms.samsungcloudsolution.com",
            "www.etracker.de",
            "www.google-analytics.com",
            "www.samsungrm.net",
            "x2.vindicosuite.com",
            "xml.opera.com",
            "xpu.samsungelectronics.com",
            "ypu.samsungelectronics.com",
            "www.google.com",
            "chip.de",
        ];*/

        $content = $this->fileHandler->getMergedFiles('./../adlist/restored');
        $content = (new SortContent())->getSortedContent($content);
        //$content = (new ValidateUrl())->getValidatedUrlsByCurl($content);
        $this->fileHandler->writeContentToFile($content, './../adlist/mergedRestoreFiles.txt');
    }
}

