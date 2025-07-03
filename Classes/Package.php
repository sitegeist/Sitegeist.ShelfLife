<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Sitegeist\ShelfLife\ContentRepository\DocumentShelfLiveUpdater;

class Package extends BasePackage
{
    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(Node::class, 'nodeAdded', DocumentShelfLiveUpdater::class, 'nodeAdded', false);
        $dispatcher->connect(Node::class, 'nodeUpdated', DocumentShelfLiveUpdater::class, 'nodeUpdated', false);
        $dispatcher->connect(Node::class, 'nodeRemoved', DocumentShelfLiveUpdater::class, 'nodeRemoved', false);
        $dispatcher->connect(Workspace::class, 'afterNodePublishing', DocumentShelfLiveUpdater::class, 'afterNodePublishing', false);
        $dispatcher->connect(PersistenceManager::class, 'allObjectsPersisted', DocumentShelfLiveUpdater::class, 'allObjectsPersisted', false);
    }
}
