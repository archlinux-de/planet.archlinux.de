<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220122121022 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item CHANGE feed_url feed_url VARCHAR(191) NOT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E6FAD93B');
        $this->addSql(
            'ALTER TABLE item CHANGE feed_url feed_url VARCHAR(191) DEFAULT NULL'
        );
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E6FAD93B FOREIGN KEY (feed_url) REFERENCES feed (url) ON DELETE CASCADE');
    }

    #[\Override]
    public function isTransactional(): bool
    {
        return false;
    }
}
