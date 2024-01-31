<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\NodeTraverser;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;

final class NodeTraverserFactory
{
    /**
     * To always register naming traverser
     * out of the box.
     */
    public static function createWithNodeVisitor(NodeVisitor $nodeVisitor): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());

        $nodeTraverser->addVisitor($nodeVisitor);

        return $nodeTraverser;
    }
}
