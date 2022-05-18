<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420060955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `payment_user_association` (
          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
          `user_id` bigint unsigned NOT NULL,
          `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `quantity` int(11) NOT NULL,
          `errors` int(11) NOT NULL,
          `created_at` datetime NOT NULL,
          `updated_at` datetime,
          PRIMARY KEY (`id`),
          KEY `payment_user_id` (`user_id`),
          CONSTRAINT `payment_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
