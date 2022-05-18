<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201936194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `event_signup_articles` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `event_signup_id` bigint(20) unsigned NOT NULL,
            `user_article_id` bigint(20) unsigned NOT NULL,
            `created_at` datetime DEFAULT NULL,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `event_signup_id` (`event_signup_id`),
            KEY `user_article_id` (`user_article_id`),
            CONSTRAINT `event_signup_articles_ibfk_1` FOREIGN KEY (`event_signup_id`) REFERENCES `event_signup` (`id`),
            CONSTRAINT `event_signup_articles_ibfk_2` FOREIGN KEY (`user_article_id`) REFERENCES `user_articles` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
