<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20250602153612 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQLPlatform'."
        );

        $documentAgesTable = $schema->createTable('sitegeist_shelflife_live_document_ages');
        $documentAgesTable->addColumn('node_identifier', Types::STRING, ['length' => 128])->setNotnull(true);
        $documentAgesTable->addColumn('dimension_hash', Types::STRING, ['length' => 128])->setNotnull(true);
        $documentAgesTable->addColumn('modification_date', Types::DATETIME_IMMUTABLE)->setNotnull(true);
        $documentAgesTable->setPrimaryKey(['node_identifier', 'dimension_hash']);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQLPlatform'."
        );

        $schema->dropTable('sitegeist_shelflife_live_document_ages');
    }
}
