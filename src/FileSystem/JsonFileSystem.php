<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\FileSystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use TomasVotruba\Finalize\Exception\ShouldNotHappenException;

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

    /**
     * @return array<string, mixed>
     */
    public static function read(string $projectHash): array
    {
        $fileContents = FileSystem::read(getcwd() . '/.finalize.json');
        $json = Json::decode($fileContents, true);

        if (! isset($json[$projectHash])) {
            throw new ShouldNotHappenException(
                'Could not read data for current project. Run family-tree command first to create it'
            );
        }

        return $json[$projectHash];
    }
}
