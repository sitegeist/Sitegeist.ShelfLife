<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife\Specification;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\ContentRepository\Domain\Service\Context;

class IsLiveSpecification
{
    public static function isSatisfiedByNode(NodeInterface $node): bool
    {
        return self::isSatisfiedByWorkspace($node->getContext()->getWorkspace());
    }

    public static function isSatisfiedByContext(Context $context): bool
    {
        return self::isSatisfiedByWorkspace($context->getWorkspace());
    }

    public static function isSatisfiedByWorkspace(Workspace $workspace): bool
    {
        return ($workspace->getBaseWorkspace() === null) && ($workspace->getName() === 'live');
    }
}
