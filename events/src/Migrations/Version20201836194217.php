<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201836194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `event_signup` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `joined_id` bigint(20) unsigned NOT NULL,
          `badge` varchar(255) NOT NULL DEFAULT \'\',
          `initial_institute` varchar(255) NOT NULL DEFAULT \'\',
          `event_payment_mode_id` int(10) unsigned NOT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `joined_id` (`joined_id`),
          KEY `event_payment_mode_id` (`event_payment_mode_id`),
          CONSTRAINT `event_signup_ibfk_1` FOREIGN KEY (`joined_id`) REFERENCES `user` (`id`),
          CONSTRAINT `event_signup_ibfk_2` FOREIGN KEY (`event_payment_mode_id`) REFERENCES `event_payment_mode` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
