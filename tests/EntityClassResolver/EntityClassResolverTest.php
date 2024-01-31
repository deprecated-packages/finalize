<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Tests\EntityClassResolver;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Finalize\DependencyInjection\ContainerFactory;
use TomasVotruba\Finalize\EntityClassResolver;
use TomasVotruba\Finalize\FileSystem\PhpFilesFinder;
use TomasVotruba\Finalize\Tests\EntityClassResolver\Fixture\AttributeMarkedEntity;
use TomasVotruba\Finalize\Tests\EntityClassResolver\Fixture\DocMarkedEntity;

final class EntityClassResolverTest extends TestCase
{
    private EntityClassResolver $entityClassResolver;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->entityClassResolver = $container->make(EntityClassResolver::class);
    }

    public function test(): void
    {
        $phpFileInfos = PhpFilesFinder::findPhpFileInfos([__DIR__ . '/Fixture']);

        $parentClassNames = $this->entityClassResolver->resolve($phpFileInfos, function () {
        });

        $this->assertSame([AttributeMarkedEntity::class, DocMarkedEntity::class], $parentClassNames);
    }
}
