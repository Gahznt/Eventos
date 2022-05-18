<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203731193957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `season` DROP `year`");
        $this->addSql("ALTER TABLE `season` ADD `event_id` INT(10)  UNSIGNED  NULL  AFTER `id`");
        $this->addSql("ALTER TABLE `season` ADD `created_at` DATETIME  NULL  AFTER `event_id`");
        $this->addSql("ALTER TABLE `season` ADD `updated_at` DATETIME  NULL  ON UPDATE CURRENT_TIMESTAMP  AFTER `created_at`");
        $this->addSql("ALTER TABLE `season` ADD `deleted_at` DATETIME  NULL  AFTER `updated_at`");
        $this->addSql("ALTER TABLE `season` ADD `position` INT  NOT NULL  DEFAULT '1'  AFTER `deleted_at`");
        $this->addSql("ALTER TABLE `season` ADD `color` VARCHAR(50)  NOT NULL  DEFAULT ''  AFTER `position`");
        $this->addSql("ALTER TABLE `season` ADD `place` VARCHAR(255)  NOT NULL  DEFAULT ''  AFTER `color`");
        $this->addSql("ALTER TABLE `season` ADD `name_portuguese` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `place`");
        $this->addSql("ALTER TABLE `season` ADD `description_portuguese` TEXT  NOT NULL  AFTER `name_portuguese`");
        $this->addSql("ALTER TABLE `season` ADD `name_english` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `description_portuguese`");
        $this->addSql("ALTER TABLE `season` ADD `description_english` TEXT  NOT NULL  AFTER `name_english`");
        $this->addSql("ALTER TABLE `season` ADD `name_spanish` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `description_english`");
        $this->addSql("ALTER TABLE `season` ADD `description_spanish` TEXT  NOT NULL  AFTER `name_spanish`");
        $this->addSql("ALTER TABLE `season` ADD `status` SMALLINT  NULL  DEFAULT '0'  AFTER `description_spanish`");
        $this->addSql("ALTER TABLE `season` ADD `is_homolog` SMALLINT  NULL  DEFAULT '0'  AFTER `status`");
        $this->addSql("ALTER TABLE `season` CHANGE `start` `date_start` DATE  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `season` MODIFY COLUMN `date_start` DATE DEFAULT NULL AFTER `place`");
        $this->addSql("ALTER TABLE `season` CHANGE `deadline` `date_end` DATE  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `season` MODIFY COLUMN `date_end` DATE DEFAULT NULL AFTER `date_start`");
        $this->addSql("ALTER TABLE `season` ADD CONSTRAINT `season_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");

        // $this->addSql("");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `season` ADD `year` MEDIUMINT(5)  NULL  DEFAULT NULL  AFTER `id`");
        $this->addSql("ALTER TABLE `season` DROP `event_id`");
        $this->addSql("ALTER TABLE `season` DROP `created_at`");
        $this->addSql("ALTER TABLE `season` DROP `updated_at`");
        $this->addSql("ALTER TABLE `season` DROP `deleted_at`");
        $this->addSql("ALTER TABLE `season` DROP `position`");
        $this->addSql("ALTER TABLE `season` DROP `color`");
        $this->addSql("ALTER TABLE `season` DROP `place`");
        $this->addSql("ALTER TABLE `season` DROP `name_portuguese`");
        $this->addSql("ALTER TABLE `season` DROP `description_portuguese`");
        $this->addSql("ALTER TABLE `season` DROP `name_english`");
        $this->addSql("ALTER TABLE `season` DROP `description_english`");
        $this->addSql("ALTER TABLE `season` DROP `name_spanish`");
        $this->addSql("ALTER TABLE `season` DROP `description_spanish`");
        $this->addSql("ALTER TABLE `season` DROP `status`");
        $this->addSql("ALTER TABLE `season` DROP `is_homolog`");
        $this->addSql("ALTER TABLE `season` CHANGE `date_start` `start` DATE  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `season` CHANGE `date_end` `deadline` DATE  NULL  DEFAULT NULL");
        $this->addSql("ALTER TABLE `season` DROP FOREIGN KEY `season_event_id`");


        // $this->addSql("");
    }
}
