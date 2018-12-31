<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial schema
 */
final class Version20181231211314 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // phpcs:disable
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, feed_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, public_id VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, last_modified DATETIME NOT NULL, link VARCHAR(255) NOT NULL, author_name VARCHAR(255) NOT NULL, author_uri VARCHAR(255) DEFAULT NULL, INDEX IDX_1F1B251E51A5BC03 (feed_id), INDEX IDX_1F1B251E270A2932 (last_modified), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feed (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(191) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, last_modified DATETIME NOT NULL, link VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_234044ABF47645AE (url), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E51A5BC03 FOREIGN KEY (feed_id) REFERENCES feed (id)');
        // phpcs:enable
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E51A5BC03');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE feed');
    }
}
