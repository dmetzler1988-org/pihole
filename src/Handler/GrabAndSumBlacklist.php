<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\FileHandler;
use App\Service\SortContent;
use App\Service\ValidateUrl;

// TODO: Create a file with each URL given on pihole (done - see blocklist-updated.txt)
// TODO: read from this file, grab content from urls and save it
// TODO: append to other restored contents
// TODO: remove url validation for these new urls from websites

class GrabAndSumBlacklist
{
    protected array $ignoredFiles = ['README.md', '.DS_Store'];
    protected array $ignoredFolders = ['.', '..'];
    protected array $ignoredLines = ['comments' => '/^\#/'];

    public function __construct()
    {
        $fileHandler = new FileHandler($this->ignoredFiles, $this->ignoredFolders, $this->ignoredLines);
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

        #$content = (new FindDuplicates())->getSingleContent();
        #$content = (new ValidateUrl())->getValidContent($content);
        #$content = (new SortContent())->getSortedContent($content);

        $content = $fileHandler->getMergedFiles('./../blacklist/available');
        $content = (new SortContent())->getSortedContent($content);

        #$content = (new ValidateUrl())->getValidatedUrlsByHeaders($content);
        #$content = (new ValidateUrl())->getValidatedUrlsByPing($content);
        #$content = (new ValidateUrl())->getValidatedUrlsByCurl($content);

        $fileHandler->writeContentToFile($content);
    }
}