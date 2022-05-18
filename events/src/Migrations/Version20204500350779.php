<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500350779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("CREATE TABLE `system_ensalement_sessions` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `edition_id` int(10) unsigned NOT NULL,
          `name` varchar(255) DEFAULT NULL,
          `type` smallint(5) DEFAULT NULL,
          `date` date DEFAULT NULL,
          `start` time DEFAULT NULL,
          `end` time DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `deleted_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `edition_id` (`edition_id`),
          CONSTRAINT `system_ensalement_sessions_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `edition` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `system_ensalement_sessions`;");
    }
}
