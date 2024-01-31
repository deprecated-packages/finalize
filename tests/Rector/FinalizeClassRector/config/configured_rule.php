<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use TomasVotruba\Finalize\Rector\FinalizeClassRector;

return RectorConfig::configure()
    ->withRules([FinalizeClassRector::class]);
