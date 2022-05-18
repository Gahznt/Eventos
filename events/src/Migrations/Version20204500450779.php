<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500450779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("CREATE TABLE `system_ensalement_slots` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `edition_id` int(10) unsigned NOT NULL,
          `system_ensalement_sessions_id` bigint(20) unsigned NOT NULL,
          `system_ensalement_rooms_id` bigint(20) unsigned NOT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `edition_id` (`edition_id`),
          KEY `system_ensalement_sessions_id` (`system_ensalement_sessions_id`),
          KEY `system_ensalement_rooms_id` (`system_ensalement_rooms_id`),
          CONSTRAINT `system_ensalement_slots_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
          CONSTRAINT `system_ensalement_slots_ibfk_2` FOREIGN KEY (`system_ensalement_sessions_id`) REFERENCES `system_ensalement_sessions` (`id`),
          CONSTRAINT `system_ensalement_slots_ibfk_3` FOREIGN KEY (`system_ensalement_rooms_id`) REFERENCES `system_ensalement_rooms` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_ensalement_slots`;");
    }
}
