<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(codeQuality: true, codingStyle: true, deadCode: true, privatization: true, naming: true, typeDeclarations: true)
    ->withImportNames(removeUnusedImports: true);
