<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Tests\ParentClassResolver;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Finalize\FileSystem\PhpFilesFinder;
use TomasVotruba\Finalize\ParentClassResolver;

final class ParentClassResolverTest extends TestCase
{
    private ParentClassResolver $parentClassResolver;

    protected function setUp(): void
    {
        $this->parentClassResolver = new ParentClassResolver();
    }

    public function test(): void
    {
        $phpFileInfos = PhpFilesFinder::findPhpFileInfos([__DIR__ . '/Fixture']);

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, function () {
        });

        $this->assertSame(
            ['TomasVotruba\Finalize\Tests\ParentClassResolver\Fixture\SomeParentClass'],
            $parentClassNames
        );
    }
}
