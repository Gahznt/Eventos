<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204700250668 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `system_evaluation_log` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_log_id` bigint(20) unsigned DEFAULT NULL,
          `sytem_evaluation_id` bigint(20) unsigned NOT NULL,
          `ip` varchar(255) DEFAULT NULL,
          `content` text,
          `status` smallint(10) DEFAULT '0',
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `sytem_evaluation_id` (`sytem_evaluation_id`),
          KEY `user_log_id` (`user_log_id`),
          CONSTRAINT `system_evaluation_log_ibfk_1` FOREIGN KEY (`sytem_evaluation_id`) REFERENCES `system_evaluation` (`id`),
          CONSTRAINT `system_evaluation_log_ibfk_2` FOREIGN KEY (`user_log_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_evaluation_log`;");
    }
}
