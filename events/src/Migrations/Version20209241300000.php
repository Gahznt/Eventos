<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209241300000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `system_evaluation_config` ADD COLUMN `ensalement_general` TINYINT(2) DEFAULT '0' AFTER `free_certiticates`;");
        $this->addSql("ALTER TABLE `system_evaluation_config` ADD COLUMN `ensalement_priority` TINYINT(2) DEFAULT '0' AFTER `ensalement_general`;");
        $this->addSql("ALTER TABLE `system_evaluation_config` ADD COLUMN `free_sections` TINYINT(2) DEFAULT '0' AFTER `ensalement_priority`;");
        $this->addSql("ALTER TABLE `system_evaluation_config` ADD COLUMN `free_signup` TINYINT(2) DEFAULT '0' AFTER `free_sections`;");
        $this->addSql("ALTER TABLE `system_evaluation_config` CHANGE `automatic_certiticates` `automatic_certiticates` TINYINT(2)  NULL  DEFAULT '0';");
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
