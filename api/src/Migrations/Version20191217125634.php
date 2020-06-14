<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191217125634 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE item DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE item DROP public_id, CHANGE feed_url feed_url VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD PRIMARY KEY (link)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // phpcs:disable
        $this->addSql('ALTER TABLE item DROP PRIMARY KEY');
        $this->addSql(
            'ALTER TABLE item ADD public_id VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE feed_url feed_url VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`'
        );
        $this->addSql('ALTER TABLE item ADD PRIMARY KEY (public_id, feed_url)');
        // phpcs:enable
    }
}
