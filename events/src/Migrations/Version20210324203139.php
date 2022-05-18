<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324203139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs


        $this->addSql("ALTER TABLE `edition_payment_mode` ADD `is_associated` TINYINT  NULL  DEFAULT '0'  AFTER `value`;");

        $this->addSql("CREATE TABLE `edition_discount` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_identifier` varchar(255) DEFAULT NULL,
          `percentage` float(12,2) DEFAULT '0.00',
          `edition_id` int(10) unsigned DEFAULT NULL,
          `is_active` TINYINT  NULL  DEFAULT '1',
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `edition_discount_edition_id` (`edition_id`),
          CONSTRAINT `edition_discount_edition_id` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->addSql("ALTER TABLE `edition_signup` ADD `edition_discount_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `edition_payment_mode_id`;");
        $this->addSql("ALTER TABLE `edition_signup` ADD CONSTRAINT `edition_signup_edition_discount_id` FOREIGN KEY (`edition_discount_id`) REFERENCES `edition_discount` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
