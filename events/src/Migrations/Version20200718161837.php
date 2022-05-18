<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200718161837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE `activities_panelist` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `activity_id` int(10) unsigned NOT NULL,
          `panelist_id` bigint(20) unsigned NOT NULL,
          `proponent_curriculum_lattes_link` mediumtext COLLATE utf8_unicode_ci,
          `proponent_curriculum_pdf_path` mediumtext COLLATE utf8_unicode_ci,
          PRIMARY KEY (`id`),
          KEY `activities_panelist_activity_id` (`activity_id`),
          KEY `activities_panelist_panelist_id` (`panelist_id`),
          CONSTRAINT `activities_panelist_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
          CONSTRAINT `activities_panelist_panelist_id` FOREIGN KEY (`panelist_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE `activities_panelist`');

    }
}
