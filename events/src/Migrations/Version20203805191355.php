<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203805191355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("CREATE TABLE `subsection` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `edition_id` int(10) unsigned DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          `type` varchar(50) NOT NULL DEFAULT '',
          `position` int(11) NOT NULL DEFAULT '1',
          `is_highlight` smallint(6) DEFAULT '0',
          `name_portuguese` varchar(255) NOT NULL DEFAULT '',
          `front_call_portuguese` varchar(255) NOT NULL DEFAULT '',
          `description_portuguese` text NOT NULL,
          `name_english` varchar(255) NOT NULL DEFAULT '',
          `front_call_english` varchar(255) NOT NULL DEFAULT '',
          `description_english` text NOT NULL,
          `name_spanish` varchar(255) NOT NULL DEFAULT '',
          `front_call_spanish` varchar(255) NOT NULL DEFAULT '',
          `description_spanish` text NOT NULL,
          `status` smallint(6) DEFAULT '0',
          `is_homolog` smallint(6) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `subsection_edition` (`edition_id`),
          CONSTRAINT `subsection_edition` FOREIGN KEY (`edition_id`) REFERENCES `season` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DROP TABLE `subsection`");
    }
}
