<?php

# rector.php
use Rector\Config\RectorConfig;
use TomasVotruba\Finalize\Rector\FinalizeClassRector;

return RectorConfig::configure()
    ->withRules([
        FinalizeClassRector::class,
    ]);
