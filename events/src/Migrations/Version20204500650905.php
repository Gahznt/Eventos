<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `coordinator_id` `coordinator_debater_1_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `debater_id` `coordinator_debater_2_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP FOREIGN KEY `system_ensalement_scheduling_ibfk_9`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `coordinator_debater_id`");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `coordinator_debater_1_type` SMALLINT(1)  UNSIGNED  NULL  DEFAULT NULL  COMMENT '1=coordinator, 2=debater, 3=coordinator/debater'  AFTER `title`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `coordinator_debater_2_type` SMALLINT(1)  UNSIGNED  NULL  DEFAULT NULL  COMMENT '1=coordinator, 2=debater, 3=coordinator/debater'  AFTER `coordinator_debater_1_id`");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `coordinator_debater_2_type`");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` DROP `coordinator_debater_1_type`");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` ADD `coordinator_debater_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");

        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `coordinator_debater_2_id` `debater_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `system_ensalement_scheduling` CHANGE `coordinator_debater_1_id` `coordinator_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL");
    }
}
