<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201633194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `user_themes_reviewers` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_themes_id` bigint(20) unsigned NOT NULL,
          `name` varchar(255) DEFAULT \'\',
          `link_lattes` varchar(255) DEFAULT NULL,
          `email` varchar(255) DEFAULT NULL,
          `phone` bigint(20) DEFAULT NULL,
          `cellphone` bigint(20) DEFAULT NULL,
          `institute` varchar(255) DEFAULT NULL,
          `program` varchar(255) DEFAULT NULL,
          `state` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_themes_id` (`user_themes_id`),
          CONSTRAINT `user_themes_reviewers_ibfk_1` FOREIGN KEY (`user_themes_id`) REFERENCES `user_themes` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
