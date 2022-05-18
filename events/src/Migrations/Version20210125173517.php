<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125173517 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles` DROP FOREIGN KEY `user_articles_ibfk_7`;");
        $this->addSql("ALTER TABLE `user_articles` CHANGE `last_id` `last_id` SMALLINT(5)  NULL  DEFAULT NULL  COMMENT 'Este trabalho é decorrente de ';");


        $this->addSql("CREATE TABLE `event_divisions` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `event_id` int(10) unsigned DEFAULT NULL,
          `division_id` int(10) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `event_divisions_division_id` (`division_id`),
          KEY `event_divisions_event_id` (`event_id`),
          CONSTRAINT `event_divisions_division_id` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          CONSTRAINT `event_divisions_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
