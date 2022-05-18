<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("CREATE TABLE `system_ensalement_scheduling_articles` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `system_ensalement_sheduling_id` bigint(20) unsigned NOT NULL,
          `user_articles_id` bigint(20) unsigned DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `system_ensalement_sheduling_id` (`system_ensalement_sheduling_id`),
          KEY `user_articles_id` (`user_articles_id`),
          CONSTRAINT `system_ensalement_scheduling_articles_ibfk_1` FOREIGN KEY (`system_ensalement_sheduling_id`) REFERENCES `system_ensalement_scheduling` (`id`),
          CONSTRAINT `system_ensalement_scheduling_articles_ibfk_2` FOREIGN KEY (`user_articles_id`) REFERENCES `user_articles` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_ensalement_scheduling_articles`;");
    }
}
