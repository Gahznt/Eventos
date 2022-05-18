<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201622194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `panels_panelist` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `panel_id` bigint(20) unsigned NOT NULL,
          `panelist_id` bigint(20) unsigned NOT NULL,
          `proponent_curriculum_lattes_link` mediumtext,
          `proponent_curriculum_pdf_path` mediumtext,
          PRIMARY KEY (`id`),
          KEY `panel_id` (`panel_id`),
          KEY `panelist_id` (`panelist_id`),
          CONSTRAINT `panels_panelist_ibfk_1` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
          CONSTRAINT `panels_panelist_ibfk_2` FOREIGN KEY (`panelist_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
