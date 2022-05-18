<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204108150568 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `panel_evaluation_log` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `panel_id` bigint(20) unsigned NOT NULL,
            `user_id` bigint(20) unsigned NOT NULL,  
            `created_at` datetime NOT NULL,
            `ip` text NOT NULL,
            `action` text NOT NULL,
            `reason` text,   
            `visible_author` boolean NOT NULL DEFAULT true,
            PRIMARY KEY (`id`),
            KEY `panel_id` (`panel_id`), 
            CONSTRAINT `panel_evaluation_log_ibfk_1` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
            CONSTRAINT `panel_evaluation_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE `panel_evaluation_log`");
    }
}
