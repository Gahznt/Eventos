<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204408250779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            CREATE TABLE `user_themes_details` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_themes_id` bigint(20) unsigned NOT NULL,
                `portuguese_description` text,
                `english_description` text,
                `spanish_description` text,
                `portuguese_title` varchar(255) DEFAULT NULL,
                `english_title` varchar(255) DEFAULT NULL,
                `spanish_title` varchar(255) DEFAULT NULL,
                `portuguese_keywords` text,
                `english_keywords` text,
                `spanish_keywords` text,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_themes_id` (`user_themes_id`),
                CONSTRAINT `user_themes_details_ibfk_1` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
        ");
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `user_themes_details`");
    }
}
