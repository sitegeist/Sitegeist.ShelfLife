<?php

declare(strict_types=1);

namespace Sitegeist\ShelfLife\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Sitegeist\ShelfLife\Specification\IsDocumentSpecification;
use Sitegeist\ShelfLife\Specification\IsLiveSpecification;

#[Flow\Scope("singleton")]
class DocumentShelfLiveRepository
{
    private const TABLE_NAME = 'sitegeist_shelflife_live_document_ages';

    public function __construct(
        private Connection $dbal
    ) {
    }

    public function updateShelfLiveForDocument(NodeInterface $node): void
    {
        if (!IsDocumentSpecification::isSatisfiedByNode($node)) {
            return;
        }
        if (!IsLiveSpecification::isSatisfiedByNode($node)) {
            return;
        }

        $this->updateShelfLiveForNodeIdentifierAndDimensionHash(
            $node->getIdentifier(),
            $node->getNodeData()->getDimensionsHash()
        );
    }

    public function updateShelfLiveForNodeIdentifierAndDimensionHash(string $nodeIdentifier, string $dimensionHash): void
    {
        $date = new \DateTimeImmutable();
        $previousDate = $this->findShelfLifeForNodeIdentifierAndDimensionHash($nodeIdentifier, $dimensionHash);

        if ($previousDate instanceof \DateTimeImmutable) {
            $this->dbal->update(
                self::TABLE_NAME,
                [
                    'node_identifier' => $nodeIdentifier,
                    'dimension_hash' => $dimensionHash,
                    'modification_date' => $date,
                ],
                [
                    'node_identifier' => $nodeIdentifier,
                    'dimension_hash' => $dimensionHash,
                ],
                [
                    'modification_date' => Types::DATETIME_IMMUTABLE,
                ]
            );
        } else {
            $this->dbal->insert(
                self::TABLE_NAME,
                [
                    'node_identifier' => $nodeIdentifier,
                    'dimension_hash' => $dimensionHash,
                    'modification_date' => $date,
                ],
                [
                    'modification_date' => Types::DATETIME_IMMUTABLE,
                ]
            );
        }
    }

    public function findShelfLifeForDocument(NodeInterface $node): ?\DateTimeImmutable
    {
        if (!IsDocumentSpecification::isSatisfiedByNode($node)) {
            return null;
        }
        if (!IsLiveSpecification::isSatisfiedByNode($node)) {
            return null;
        }
        return $this->findShelfLifeForNodeIdentifierAndDimensionHash(
            $node->getIdentifier(),
            $node->getNodeData()->getDimensionsHash(),
        );
    }

    public function findShelfLifeForNodeIdentifierAndDimensionHash(string $nodeIdentifier, string $dimensionHash): ?\DateTimeImmutable
    {
        $table = self::TABLE_NAME;
        $query = <<<SQL
            SELECT *
            FROM {$table}
            WHERE node_identifier = :node_identifier
            AND dimension_hash = :dimension_hash
        SQL;

        $row = $this->dbal->fetchAssociative(
            $query,
            [
                'node_identifier' => $nodeIdentifier,
                'dimension_hash' => $dimensionHash,
            ]
        );

        if ($row === false) {
            return null;
        }

        if (is_array($row) && array_key_exists('modification_date', $row)) {
            return self::parseDateTimeString($row['modification_date']);
        }
        return null;
    }

    private static function parseDateTimeString(string $string): \DateTimeImmutable
    {
        $result = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $string);
        if ($result === false) {
            throw new \RuntimeException(sprintf('Failed to parse "%s" into a valid DateTime', $string), 1678902055);
        }
        return $result;
    }
}
