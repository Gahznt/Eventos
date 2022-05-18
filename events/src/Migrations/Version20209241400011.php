<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209241400011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `user_articles` ADD `original_user_themes_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL  AFTER `user_themes_id`;");
        $this->addSql("ALTER TABLE `user_articles` ADD CONSTRAINT `original_user_themes_id` FOREIGN KEY (`original_user_themes_id`) REFERENCES `user_themes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `user_articles` DROP FOREIGN KEY `original_user_themes_id`;");
        $this->addSql("ALTER TABLE `user_articles` DROP `original_user_themes_id`;");
    }
}
