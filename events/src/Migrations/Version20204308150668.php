<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204308150668 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles` ADD `status` SMALLINT(5)  NULL  DEFAULT 0  AFTER `premium`;");
        $this->addSql("ALTER TABLE `user_articles` ADD `created_at` DATETIME  NULL  AFTER `status`;");
        $this->addSql("ALTER TABLE `user_articles` ADD `deleted_at` DATETIME  NULL  AFTER `created_at`;");
        $this->addSql("ALTER TABLE `user_articles` ADD `updated_at` DATETIME  NULL  ON UPDATE CURRENT_TIMESTAMP  AFTER `deleted_at`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles` DROP `status`;");
        $this->addSql("ALTER TABLE `user_articles` DROP `created_at`;");
        $this->addSql("ALTER TABLE `user_articles` DROP `deleted_at`;");
        $this->addSql("ALTER TABLE `user_articles` DROP `updated_at`;");
    }
}
