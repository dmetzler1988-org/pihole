<?php

declare(strict_types=1);

namespace App;

use App\Handler\CleanupRestoredFiles;
use App\Handler\GrabAndSumBlacklist;

require '../vendor/autoload.php';

// TODO: make output to csv, txt and simple echo to choose via option on run command

#new CleanupRestoredFiles());
(new GrabAndSumBlacklist());
