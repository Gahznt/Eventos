<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203728200909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `event` CHANGE `name` `name_portuguese` VARCHAR(255) NOT NULL  DEFAULT ''");
        $this->addSql("ALTER TABLE `event` ADD `created_at` DATETIME  NULL  AFTER `id`");
        $this->addSql("ALTER TABLE `event` ADD `updated_at` DATETIME  NULL  ON UPDATE CURRENT_TIMESTAMP  AFTER `created_at`");
        $this->addSql("ALTER TABLE `event` ADD `deleted_at` DATETIME  NULL  AFTER `updated_at`");
        $this->addSql("ALTER TABLE `event` ADD `title_portuguese` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `name_portuguese`");
        $this->addSql("ALTER TABLE `event` ADD `description_portuguese` TEXT  NOT NULL  AFTER `title_portuguese`");
        $this->addSql("ALTER TABLE `event` ADD `name_english` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `description_portuguese`");
        $this->addSql("ALTER TABLE `event` ADD `title_english` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `name_english`");
        $this->addSql("ALTER TABLE `event` ADD `description_english` TEXT  NOT NULL  AFTER `title_english`");
        $this->addSql("ALTER TABLE `event` ADD `name_spanish` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `description_english`");
        $this->addSql("ALTER TABLE `event` ADD `title_spanish` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `name_spanish`");
        $this->addSql("ALTER TABLE `event` ADD `description_spanish` TEXT  NOT NULL  AFTER `title_spanish`");
        $this->addSql("ALTER TABLE `event` ADD `status` SMALLINT  NULL  DEFAULT '0'  AFTER `description_spanish`");
        $this->addSql("ALTER TABLE `event` ADD `is_homolog` SMALLINT  NULL  DEFAULT '0'  AFTER `status`");
        // $this->addSql("");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `event` CHANGE `name_portuguese` `name` VARCHAR(255) NOT NULL  DEFAULT ''");
        $this->addSql("ALTER TABLE `event` DROP `created_at`");
        $this->addSql("ALTER TABLE `event` DROP `updated_at`");
        $this->addSql("ALTER TABLE `event` DROP `deleted_at`");
        $this->addSql("ALTER TABLE `event` DROP `title_portuguese`");
        $this->addSql("ALTER TABLE `event` DROP `description_portuguese`");
        $this->addSql("ALTER TABLE `event` DROP `name_english`");
        $this->addSql("ALTER TABLE `event` DROP `title_english`");
        $this->addSql("ALTER TABLE `event` DROP `description_english`");
        $this->addSql("ALTER TABLE `event` DROP `name_spanish`");
        $this->addSql("ALTER TABLE `event` DROP `title_spanish`");
        $this->addSql("ALTER TABLE `event` DROP `description_spanish`");
        $this->addSql("ALTER TABLE `event` DROP `status`");
        $this->addSql("ALTER TABLE `event` DROP `is_homolog`");
        // $this->addSql("");
    }
}
