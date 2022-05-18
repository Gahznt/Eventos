<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650894 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `edition_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `id`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `divison_id` `division_id` INT(10)  UNSIGNED  NOT NULL");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `panel_id` bigint(20) unsigned  AFTER `activity_id`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");

        $this->addSql("ALTER TABLE `activity` CHANGE `time_restriction` `time_restriction` VARCHAR(255)  NOT NULL  DEFAULT ''");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `content_type` SMALLINT(1)  UNSIGNED  NULL  DEFAULT NULL AFTER `division_id`");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_11`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `panel_id`");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_10`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `edition_id`");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `division_id` `divison_id` INT(10)  UNSIGNED  NOT NULL");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `content_type`");
    }
}
