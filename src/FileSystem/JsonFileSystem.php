<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\FileSystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class JsonFileSystem
{
    /**
     * @param array<string, mixed> $data
     */
    public static function writeCacheFile(string $projectHash, array $data): void
    {
        $namespacedData = [
            $projectHash => $data,
        ];

        $jsonContents = Json::encode($namespacedData, pretty: true);
        FileSystem::write(getcwd() . '/.finalize.json', $jsonContents);
    }
}
