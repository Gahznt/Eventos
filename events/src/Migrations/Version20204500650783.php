<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650783 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_4`;)");
        $this->addSql("ALTER TABLE `user_articles` CHANGE theme_id user_themes_id int(10) unsigned NULL;");

        $this->addSql("ALTER TABLE `user_articles` CHANGE user_themes_id user_themes_id BIGINT(20) unsigned NULL;");
        $this->addSql("ALTER TABLE `user_articles` ADD FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`);");

        $this->addSql("ALTER TABLE `user_articles` ADD `keywords` text AFTER `status`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
