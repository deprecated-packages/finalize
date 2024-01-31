<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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

        // make final
        $node->flags = $node->flags | Class_::MODIFIER_FINAL;

        return $node;
    }
}
