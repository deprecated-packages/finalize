<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

final class EntityClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $entityClassNames = [];

    public function enterNode(Node $node)
    {
        if (! $node instanceof Class_) {
            return null;
        }

        // @todo improve with attributes
        if ($this->hasEntityDocBlock($node)) {
            $this->entityClassNames[] = $node->namespacedName->toString();
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getEntityClassNames(): array
    {
        $uniqueEntityClassNames = array_unique($this->entityClassNames);
        sort($uniqueEntityClassNames);

        return $uniqueEntityClassNames;
    }

    private function hasEntityDocBlock(Class_ $node): bool
    {
        $docComment = $node->getDocComment();
        if ($docComment instanceof Doc) {
            // dummy check
            if (! str_contains($docComment->getText(), '@')) {
                return false;
            }

            if (str_contains($docComment->getText(), 'Entity')) {
                return true;
            }

            if (str_contains($docComment->getText(), 'Embeddable')) {
                return true;
            }
        }

        return false;
    }
}
