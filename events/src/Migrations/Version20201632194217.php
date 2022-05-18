<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201632194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `user_themes_researchers` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_themes_id` bigint(20) unsigned NOT NULL,
          `researcher_id` bigint(20) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_themes_id` (`user_themes_id`),
          KEY `researcher_id` (`researcher_id`),
          CONSTRAINT `user_themes_researchers_ibfk_1` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`),
          CONSTRAINT `user_themes_researchers_ibfk_2` FOREIGN KEY (`researcher_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
