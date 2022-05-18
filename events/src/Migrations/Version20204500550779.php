<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500550779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("CREATE TABLE `system_ensalement_scheduling` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `divison_id` int(10) unsigned NOT NULL,
          `activity_id` bigint(20) unsigned DEFAULT NULL,
          `system_ensalement_slots_id` bigint(20) unsigned NOT NULL,
          `user_themes_id` bigint(20) unsigned DEFAULT NULL,
          `accept` tinyint(2) DEFAULT '0',
          `date` date DEFAULT NULL,
          `time` time DEFAULT NULL,
          `priority` tinyint(2) DEFAULT '0',
          `language` smallint(5) DEFAULT NULL,
          `format` smallint(5) DEFAULT NULL,
          `title` varchar(255) DEFAULT NULL,
          `coordinator_id` bigint(20) unsigned DEFAULT NULL,
          `debater_id` bigint(20) unsigned DEFAULT NULL,
          `coordinator_debater_id` bigint(20) unsigned DEFAULT NULL,
          `user_register_id` bigint(20) unsigned DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `divison_id` (`divison_id`),
          KEY `activity_id` (`activity_id`),
          KEY `system_ensalement_slots_id` (`system_ensalement_slots_id`),
          KEY `user_themes_id` (`user_themes_id`),
          KEY `user_register_id` (`user_register_id`),
          KEY `coordinator_id` (`coordinator_id`),
          KEY `debater_id` (`debater_id`),
          KEY `coordinator_debater_id` (`coordinator_debater_id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_1` FOREIGN KEY (`divison_id`) REFERENCES `division` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_4` FOREIGN KEY (`system_ensalement_slots_id`) REFERENCES `system_ensalement_slots` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_5` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_6` FOREIGN KEY (`user_register_id`) REFERENCES `user` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_7` FOREIGN KEY (`coordinator_id`) REFERENCES `user` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_8` FOREIGN KEY (`debater_id`) REFERENCES `user` (`id`),
          CONSTRAINT `system_ensalement_scheduling_ibfk_9` FOREIGN KEY (`coordinator_debater_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_ensalement_scheduling`;");
    }
}
