<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204700350668 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `system_evaluation_averages` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` bigint(20) unsigned NOT NULL,
          `division_id` int(10) unsigned NOT NULL,
          `edition_id` int(10) unsigned NOT NULL,
          `point_primary` decimal(10,1) DEFAULT NULL,
          `point_secondary` decimal(10,1) DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `division_id` (`division_id`),
          KEY `edition_id` (`edition_id`),
          CONSTRAINT `system_evaluation_averages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
          CONSTRAINT `system_evaluation_averages_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
          CONSTRAINT `system_evaluation_averages_ibfk_3` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_evaluation_averages`;");
    }
}