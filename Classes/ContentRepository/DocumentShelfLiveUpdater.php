<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife\ContentRepository;

use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Neos\Controller\CreateContentContextTrait;
use Sitegeist\ShelfLife\Repository\DocumentShelfLiveRepository;
use Sitegeist\ShelfLife\Specification\IsDocumentSpecification;
use Sitegeist\ShelfLife\Specification\IsLiveSpecification;

#[Flow\Scope('singleton')]
class DocumentShelfLiveUpdater
{
    use CreateContentContextTrait;

    #[Flow\Inject]
    protected DocumentShelfLiveRepository $documentShelfliveRepository;

    /**
     * @var array<string, string[]>
     */
    protected array $dimensionHashAndNodeIdentifiersToUpdate = [];

    /**
     * @see Node::emitNodeAdded()
     */
    public function nodeAdded(NodeInterface $node): void
    {
        if (!IsLiveSpecification::isSatisfiedByNode($node)) {
            return;
        }
        $this->addClosestDocumentTooShelfLifeUpdateList($node);
    }

    /**
     * @see Node::emitNodeUpdated()
     */
    public function nodeUpdated(NodeInterface $node): void
    {
        if (!IsLiveSpecification::isSatisfiedByNode($node)) {
            return;
        }
        $this->addClosestDocumentTooShelfLifeUpdateList($node);
    }

    /**
     * @see Node::emitNodeRemoved()
     */
    public function nodeRemoved(NodeInterface $node): void
    {
        if (!IsLiveSpecification::isSatisfiedByNode($node)) {
            return;
        }
        $this->addClosestDocumentTooShelfLifeUpdateList($node);
    }

    /**
     * @see Workspace::emitAfterNodePublishing()
     */
    public function afterNodePublishing(NodeInterface $node, Workspace $workspace): void
    {
        if (!IsLiveSpecification::isSatisfiedByWorkspace($workspace)) {
            return;
        }
        $this->addClosestDocumentTooShelfLifeUpdateList($node);
    }

    public function addClosestDocumentTooShelfLifeUpdateList(NodeInterface $node): void
    {
        if (IsDocumentSpecification::isSatisfiedByNode($node)) {
            $documentNode = $node;
        } else {
            $documentNode = FlowQuery::q($node)->closest('[instanceof Neos.Neos:Document]')->get(0);
        }

        $dimensionHash = $node->getNodeData()->getDimensionsHash();
        $nodeIdentifier = $documentNode->getIdentifier();

        if (array_key_exists($dimensionHash, $this->dimensionHashAndNodeIdentifiersToUpdate)) {
            $nodeIdenfiersToUpdate = $this->dimensionHashAndNodeIdentifiersToUpdate[$dimensionHash];
            if (!in_array($nodeIdentifier, $nodeIdenfiersToUpdate)) {
                $nodeIdenfiersToUpdate[] = $nodeIdentifier;
                $this->dimensionHashAndNodeIdentifiersToUpdate[$dimensionHash] = $nodeIdenfiersToUpdate;
            }
        } else {
            $this->dimensionHashAndNodeIdentifiersToUpdate[$dimensionHash] = [$nodeIdentifier];
        }
    }

    /**
     * @see PersistenceManager::emitAllObjectsPersisted()
     */
    public function allObjectsPersisted(): void
    {
        foreach ($this->dimensionHashAndNodeIdentifiersToUpdate as $dimensionHash => $nodeIdentifiers) {
            foreach ($nodeIdentifiers as $nodeIdentifier) {
                $this->documentShelfliveRepository->updateShelfLiveForNodeIdentifierAndDimensionHash(
                    $nodeIdentifier,
                    $dimensionHash
                );
            }
        }
        $this->dimensionHashAndNodeIdentifiersToUpdate = [];
    }
}
