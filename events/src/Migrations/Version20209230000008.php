<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209230000008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `certificate_download`;");
        $this->addSql("DROP TABLE `certificate_list`;");

        $this->addSql("
            CREATE TABLE `certificate_list` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `edition_id` int(10) unsigned NOT NULL,
                `user_id` bigint(20) unsigned NOT NULL,	
                `certificate_id` bigint(20) unsigned NOT NULL,
                `system_user_id` bigint(20) unsigned NOT NULL,	
                `unique_params` varchar(100) NOT NULL,	
                `view_params` text NOT NULL,
                `manual` boolean NOT NULL DEFAULT false,
                `active` boolean NOT NULL DEFAULT TRUE,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `certificate_list_unq` (`edition_id`,`user_id`,`certificate_id`,`unique_params`),
                KEY `edition_id` (`edition_id`),
                KEY `user_id` (`user_id`),
                KEY `certificate_id` (`certificate_id`),
                CONSTRAINT `certificate_list_edition_fkey` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
                CONSTRAINT `certificate_list_user_fkey` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
                CONSTRAINT `certificate_list_certificate_fkey` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`id`), 	
                CONSTRAINT `certificate_list_suser_fkey` FOREIGN KEY (`system_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->addSql("
            CREATE TABLE `certificate_download` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `certificate_list_id` bigint(20) unsigned NOT NULL, 
                `certificate_layout_id` bigint(20) unsigned NULL,
                `certificate_file` varchar(255) NOT NULL,
                `downloaded_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `certificate_download_list_unq` (`certificate_list_id`),
                KEY `certificate_list_id` (`certificate_list_id`),
                CONSTRAINT `certificate_download_list_fkey` FOREIGN KEY (`certificate_list_id`) REFERENCES `certificate_list` (`id`),
                CONSTRAINT `certificate_download_layout_fkey` FOREIGN KEY (`certificate_layout_id`) REFERENCES `certificate_layout` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
