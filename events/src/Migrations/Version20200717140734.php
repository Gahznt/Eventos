<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717140734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `activity` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title_portuguese` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `title_english` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `title_spanish` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `description_portuguese` text COLLATE utf8_unicode_ci NOT NULL,
          `description_english` text COLLATE utf8_unicode_ci NOT NULL,
          `description_spanish` text COLLATE utf8_unicode_ci NOT NULL,
          `event_id` int(10) unsigned NOT NULL,
          `activity_type` int(11) NOT NULL,
          `division_id` int(10) unsigned NOT NULL,
          `language` int(11) NOT NULL,
          `time_restriction` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `event_id` (`event_id`),
          KEY `division_id` (`division_id`),
          CONSTRAINT `division_id` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE `activity`');

    }
}
