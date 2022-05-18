<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201621194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `panel` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `division_id` int(10) unsigned NOT NULL,
              `season_id` int(10) unsigned DEFAULT NULL,
              `language` tinyint(1) DEFAULT \'0\',
              `title` varchar(255) DEFAULT NULL,
              `justification` text,
              `suggestion` text,
              `proponent_id` bigint(20) unsigned NOT NULL,
              `proponent_curriculum_lattes_link` mediumtext,
              `proponent_curriculum_pdf_path` mediumtext,
              PRIMARY KEY (`id`),
              KEY `division_id` (`division_id`),
              KEY `season_id` (`season_id`),
              KEY `proponent_id` (`proponent_id`),
              CONSTRAINT `panel_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`),
              CONSTRAINT `panel_ibfk_2` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`),
              CONSTRAINT `panel_ibfk_3` FOREIGN KEY (`proponent_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
