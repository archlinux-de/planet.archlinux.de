<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial schema
 */
final class Version20190101125607 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // phpcs:disable
        $this->addSql('CREATE TABLE item (public_id VARCHAR(191) NOT NULL, feed_url VARCHAR(191) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, last_modified DATETIME NOT NULL, link VARCHAR(255) NOT NULL, author_name VARCHAR(255) DEFAULT NULL, author_uri VARCHAR(255) DEFAULT NULL, INDEX IDX_1F1B251E6FAD93B (feed_url), INDEX IDX_1F1B251E270A2932 (last_modified), PRIMARY KEY(public_id, feed_url)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed (url VARCHAR(191) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, last_modified DATETIME NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(url)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E6FAD93B FOREIGN KEY (feed_url) REFERENCES feed (url) ON DELETE CASCADE');
        // phpcs:enable
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E6FAD93B');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE feed');
    }
}
