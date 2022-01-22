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

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE item CHANGE feed_url feed_url VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`'
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
