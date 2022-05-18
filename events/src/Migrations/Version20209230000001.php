<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209230000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `certificate_list` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `edition_id` int(10) unsigned NOT NULL,
            `user_id` bigint(20) unsigned NOT NULL,
            `certificate_id` bigint(20) unsigned NOT NULL,
            `division_id` int(10) unsigned DEFAULT NULL,
            `user_themes_id` bigint(20) unsigned DEFAULT NULL,
            `user_articles_id` bigint(20) unsigned DEFAULT NULL,	
            `info` varchar(255)	NOT NULL,
            `active` boolean NOT NULL,
            `created_at` datetime DEFAULT NULL,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `certificate_list_unq` (`edition_id`, `user_id`, `certificate_id`),
            KEY `edition_id` (`edition_id`),
            KEY `certificate_id` (`certificate_id`),	
            KEY `user_id` (`user_id`),
            CONSTRAINT `certificate_list_user_fkey` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
            CONSTRAINT `certificate_list_edition_fkey` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
            CONSTRAINT `certificate_list_certificate_fkey` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`id`), 	
            CONSTRAINT `certificate_list_division_fkey` FOREIGN KEY  (`division_id`) REFERENCES `division` (`id`),
            CONSTRAINT `certificate_list_theme_fkey` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`),
            CONSTRAINT `certificate_list_article_fkey` FOREIGN KEY (`user_articles_id`) REFERENCES `user_articles` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql("DROP TABLE `certificate_list`;");
    }
}
