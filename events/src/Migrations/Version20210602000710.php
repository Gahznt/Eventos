<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210602000710 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("CREATE TABLE `thesis` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` bigint(20) unsigned DEFAULT NULL,
          `edition_id` int(10) unsigned NOT NULL,
          `title` varchar(255) DEFAULT NULL,
          `division_id` int(10) unsigned DEFAULT NULL,
          `user_themes_id` bigint(20) unsigned DEFAULT NULL,
          `language` smallint(5) DEFAULT NULL,
          `modality` varchar(255) DEFAULT NULL,
          `advisor_name` varchar(255) DEFAULT NULL,
          `thesis_file_path` varchar(255) DEFAULT NULL,
          `agreement_file_path` varchar(255) DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `deleted_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `edition_id` (`edition_id`),
          KEY `division_id` (`division_id`),
          KEY `user_themes_id` (`user_themes_id`),
          CONSTRAINT `thesis_division_id` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
          CONSTRAINT `thesis_edition_id` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
          CONSTRAINT `thesis_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
          CONSTRAINT `thesis_user_themes_id` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
