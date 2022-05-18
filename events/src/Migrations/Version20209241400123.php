<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209241400123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `user_themes_evaluation_log` CHANGE `ip` `ip` TEXT  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL;");
        $this->addSql("ALTER TABLE `user_themes_evaluation_log` CHANGE `user_id` `user_id` BIGINT(20)  UNSIGNED  NULL;");

        //$this->addSql("ALTER TABLE `system_evaluation_config` DROP FOREIGN KEY `system_evaluation_config_ibfk_2`;");


    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
