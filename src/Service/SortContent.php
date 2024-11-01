<?php

declare(strict_types=1);

namespace App\Service;

class SortContent
{
    public function __construct()
    {
    }

    public function getSortedContent(array $content): ?array
    {
        natsort($content);

        return $content;
    }
}
