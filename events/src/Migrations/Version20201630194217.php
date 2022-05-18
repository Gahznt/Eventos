<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201630194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `user_themes` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `division_id` int(10) unsigned NOT NULL,
          `season_id` int(10) unsigned NOT NULL,
          `portuguese_description` text,
          `english_description` text,
          `spanish_description` text,
          `portuguese_title` varchar(255) DEFAULT NULL,
          `english_title` varchar(255) DEFAULT NULL,
          `spanish_title` varchar(255) DEFAULT NULL,
          `portuguese_keywords` text,
          `english_keywords` text,
          `spanish_keywords` text,
          PRIMARY KEY (`id`),
          KEY `division_id` (`division_id`),
          KEY `season_id` (`season_id`),
          CONSTRAINT `user_themes_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
          CONSTRAINT `user_themes_ibfk_2` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
