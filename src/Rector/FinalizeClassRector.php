<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TomasVotruba\Finalize\Exception\ShouldNotHappenException;
use TomasVotruba\Finalize\FileSystem\JsonFileSystem;

final class FinalizeClassRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Finalize a class, unless excluded parent class, or Doctrine entity. It requires to run "class-tree" command first, with .finalize.json file',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class SomeClass
{
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
final class SomeClass
{
}
CODE_SAMPLE
                ),

            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Class_
    {
        // skip obvious cases
        if ($node->isFinal() || $node->isAbstract() || $node->isAnonymous()) {
            return null;
        }

        // load current project dumper parent and doctrine classes
        $finalizeFilePath = getcwd() . '/.finalize.json';
        if (! file_exists($finalizeFilePath)) {
            throw new ShouldNotHappenException(sprintf(
                'The "%s" file is missing. Run family-tree command first to create it',
                $finalizeFilePath
            ));
        }

        $projectHash = Strings::webalize(getcwd());
        $projectJson = JsonFileSystem::read($projectHash);

        $protectedClassNames = array_merge($projectJson['parent_class_names'], $projectJson['entity_class_names']);

        // this class is protected, skip it
        if ($this->isNames($node, $protectedClassNames)) {
            return null;
        }

        // make final
        $node->flags = $node->flags | Class_::MODIFIER_FINAL;

        return $node;
    }
}
