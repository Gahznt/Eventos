<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200720205200 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE `activities_guest` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `activity_id` int(10) unsigned NOT NULL,
          `guest_id` bigint(20) unsigned NOT NULL,
          `proponent_curriculum_lattes_link` mediumtext COLLATE utf8_unicode_ci,
          `proponent_curriculum_pdf_path` mediumtext COLLATE utf8_unicode_ci,
          PRIMARY KEY (`id`),
          KEY `activities_guest_activity_id` (`activity_id`),
          KEY `activities_guest_guest_id` (`guest_id`),
          CONSTRAINT `activities_guest_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
          CONSTRAINT `activities_guest_guest_id` FOREIGN KEY (`guest_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE `activities_guest`');

    }
}
