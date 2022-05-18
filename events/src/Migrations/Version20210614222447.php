<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210614222447 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("DROP TABLE `certificate_download`;");
        $this->addSql("DROP TABLE `certificate_list`;");
        $this->addSql("DROP TABLE `certificate_layout`;");
        $this->addSql("DROP TABLE `certificate`;");

        $this->addSql("
            CREATE TABLE `certificate` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `edition_id` int(10) unsigned DEFAULT NULL,
              `user_id` bigint(20) unsigned DEFAULT NULL,
              `type` smallint(6) DEFAULT NULL,
              `is_active` tinyint(1) DEFAULT '0',
              `html` text,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `edition_id` (`edition_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `FK_219CDA4A74281A5E` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`),
              CONSTRAINT `FK_219CDA4AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
