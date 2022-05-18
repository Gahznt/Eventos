<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20203907150337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `system_evaluation` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `user_owner_id` bigint(20) unsigned NOT NULL,
              `user_articles_id` bigint(20) unsigned NOT NULL,
              `justification` text,
              `criteria_one` tinyint(2) DEFAULT '0',
              `criteria_two` tinyint(2) DEFAULT '0',
              `criteria_three` tinyint(2) DEFAULT '0',
              `criteria_four` tinyint(2) DEFAULT '0',
              `criteria_five` tinyint(2) DEFAULT '0',
              `criteria_six` tinyint(2) DEFAULT '0',
              `criteria_seven` tinyint(2) DEFAULT '0',
              `criteria_eight` tinyint(2) DEFAULT '0',
              `criteria_nine` tinyint(2) DEFAULT '0',
              `criteria_ten` tinyint(2) DEFAULT '0',
              `criteria_eleven` tinyint(2) DEFAULT '0',
              `format_error_at` datetime DEFAULT NULL,
              `format_error_justification` text,
              `reject_at` datetime DEFAULT NULL,
              `reject_justification` text,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `user_articles_id` (`user_articles_id`),
              KEY `user_owner_id` (`user_owner_id`),
              CONSTRAINT `system_evaluation_ibfk_1` FOREIGN KEY (`user_articles_id`) REFERENCES `user_articles` (`id`),
              CONSTRAINT `system_evaluation_ibfk_2` FOREIGN KEY (`user_owner_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_evaluation`");
    }
}
