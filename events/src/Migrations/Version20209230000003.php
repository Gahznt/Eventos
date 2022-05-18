<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209230000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `certificate_download` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `certificate_list_id` bigint(20) unsigned NOT NULL, 
            `certificate_layout_id` bigint(20) unsigned NOT NULL,
            `certificate_file` varchar(255) NOT NULL,
            `downloaded_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `certificate_download_list_unq` (`certificate_list_id`),
            KEY `certificate_list_id` (`certificate_list_id`),
            CONSTRAINT `certificate_download_list_fkey` FOREIGN KEY (`certificate_list_id`) REFERENCES `certificate_list` (`id`),
            CONSTRAINT `certificate_download_layout_fkey` FOREIGN KEY (`certificate_layout_id`) REFERENCES `certificate_layout` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `certificate_download`;");
    }
}
