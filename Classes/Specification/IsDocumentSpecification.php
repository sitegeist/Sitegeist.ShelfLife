<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife\Specification;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\Domain\Model\Workspace;

class IsDocumentSpecification
{
    public static function isSatisfiedByNode(NodeInterface $node): bool
    {
        return self::isSatisfiedByNodeType($node->getNodeType());
    }

    public static function isSatisfiedByNodeType(NodeType $nodeType): bool
    {
        return $nodeType->isOfType('Neos.Neos:Document');
    }
}
