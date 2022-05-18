<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204108150357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `division_coordinator` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `coordinator_id` bigint(20) unsigned NOT NULL,
              `division_id` int(10) unsigned NOT NULL,
              `edition_id` int(10) unsigned NOT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `coordinator_id` (`coordinator_id`),
              KEY `division_id` (`division_id`),
              KEY `edition_id` (`edition_id`),
              CONSTRAINT `division_coordinator_ibfk_1` FOREIGN KEY (`coordinator_id`) REFERENCES `user` (`id`),
              CONSTRAINT `division_coordinator_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
              CONSTRAINT `division_coordinator_ibfk_3` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `division_coordinator`");
    }
}