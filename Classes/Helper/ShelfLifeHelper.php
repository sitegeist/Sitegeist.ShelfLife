<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife\Helper;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;
use Sitegeist\ShelfLife\Repository\DocumentShelfLiveRepository;

class ShelfLifeHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var DocumentShelfLiveRepository
     */
    protected $shelfLifeRepository;

    public function documentModificationDate(NodeInterface $node): \DateTimeInterface
    {
        $dateFromNode = $node->getNodeData()->getLastModificationDateTime();
        $dateFromShelfLifeRepository = $this->shelfLifeRepository->findShelfLifeForDocument($node);
        return ($dateFromShelfLifeRepository && $dateFromShelfLifeRepository > $dateFromNode) ? $dateFromShelfLifeRepository : $dateFromNode;
    }

    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
