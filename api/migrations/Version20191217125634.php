<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191217125634 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE item DROP public_id, CHANGE feed_url feed_url VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD PRIMARY KEY (link)');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP PRIMARY KEY');
        $this->addSql(
            'ALTER TABLE item ADD public_id VARCHAR(191) NOT NULL, CHANGE feed_url feed_url VARCHAR(191) NOT NULL'
        );
        $this->addSql('ALTER TABLE item ADD PRIMARY KEY (public_id, feed_url)');
    }

    #[\Override]
    public function isTransactional(): bool
    {
        return false;
    }
}
