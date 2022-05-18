<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420061002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `payment_user_association_details` (
          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
          `payment_user_association_id` bigint unsigned NOT NULL,
          `bank_slip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `bank_slip_amount` decimal(11,2) NOT NULL,
          `fee_amount` decimal(11,2) NOT NULL,
          `net_amount` decimal(11,2) NOT NULL,
          `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
          `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `operation` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
          `payday` date NOT NULL,
          `due_date` date NOT NULL,
          `created_at` datetime NOT NULL,
          `updated_at` datetime,
          PRIMARY KEY (`id`),
          KEY `payment_user_association_id` (`payment_user_association_id`),
          CONSTRAINT `payment_user_association_fk` FOREIGN KEY (`payment_user_association_id`) REFERENCES `payment_user_association` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
