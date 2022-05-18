<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203807150327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `speaker` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `edition_id` int(10) unsigned DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 = Nacional, 1 = Internacional',
          `position` int(11) NOT NULL DEFAULT '1',
          `picture_path` varchar(255) DEFAULT NULL,
          `status` smallint(6) DEFAULT '0',
          `is_homolog` smallint(6) DEFAULT '0',
          `name_portuguese` varchar(255) NOT NULL DEFAULT '',
          `curriculum_link_portuguese` varchar(255) DEFAULT NULL,
          `content_portuguese` text NOT NULL,
          `name_english` varchar(255) NOT NULL,
          `curriculum_link_english` varchar(255) DEFAULT NULL,
          `content_english` text NOT NULL,
          `name_spanish` varchar(255) NOT NULL,
          `curriculum_link_spanish` varchar(255) DEFAULT NULL,
          `content_spanish` text NOT NULL,
          PRIMARY KEY (`id`),
          KEY `edition_id` (`edition_id`),
          CONSTRAINT `speaker_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `speaker`");
    }
}
