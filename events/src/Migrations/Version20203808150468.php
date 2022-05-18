<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203808150468 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("RENAME TABLE `event_payment_mode` TO `edition_payment_mode`");
        $this->addSql("ALTER TABLE `edition_payment_mode` ADD `edition_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `deleted_at`");
        $this->addSql("ALTER TABLE `edition_payment_mode` ADD CONSTRAINT `edition_payment_mode_edition_id` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION");
        $this->addSql("ALTER TABLE `event_signup` CHANGE `event_payment_mode_id` `edition_payment_mode_id` INT(10)  UNSIGNED  NOT NULL");

        $this->addSql("ALTER TABLE `subsection` CHANGE `description_spanish` `description_spanish` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL");
        $this->addSql("ALTER TABLE `subsection` CHANGE `description_english` `description_english` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL");
        $this->addSql("ALTER TABLE `subsection` CHANGE `description_portuguese` `description_portuguese` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `event_signup` CHANGE `edition_payment_mode_id` `event_payment_mode_id` INT(10)  UNSIGNED  NOT NULL");
        $this->addSql("ALTER TABLE `edition_payment_mode` DROP FOREIGN KEY `edition_payment_mode_edition_id`");
        $this->addSql("ALTER TABLE `edition_payment_mode` DROP `edition_id`");
        $this->addSql("RENAME TABLE `edition_payment_mode` TO `event_payment_mode`");
    }
}
