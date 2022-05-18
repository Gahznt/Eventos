<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500150779 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `activities_panelist` DROP FOREIGN KEY `activities_panelist_activity_id`;");
        $this->addSql("ALTER TABLE `activities_guest` DROP FOREIGN KEY `activities_guest_activity_id`;");
        $this->addSql("ALTER TABLE `activity` CHANGE `id` `id` BIGINT(20)  UNSIGNED  NOT NULL  AUTO_INCREMENT;");
        $this->addSql("ALTER TABLE `activities_guest` CHANGE `activity_id` `activity_id` BIGINT(20)  UNSIGNED  NOT NULL;");
        $this->addSql("ALTER TABLE `activities_panelist` CHANGE `activity_id` `activity_id` BIGINT(20)  UNSIGNED  NOT NULL;");
        $this->addSql("ALTER TABLE `activities_guest` ADD FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`);");
        $this->addSql("ALTER TABLE `activities_panelist` ADD FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `activities_panelist` DROP FOREIGN KEY `activities_panelist_activity_id`");
        $this->addSql("ALTER TABLE `activities_guest` DROP FOREIGN KEY `activities_panelist_activity_id`");
        $this->addSql("ALTER TABLE `activity` CHANGE `id` `id` INT(10)  UNSIGNED  NOT NULL  AUTO_INCREMENT;");
        $this->addSql("ALTER TABLE `activities_guest` CHANGE `activity_id` `activity_id` INT(10)  UNSIGNED  NOT NULL;");
        $this->addSql("ALTER TABLE `activities_guest` ADD FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`);");
        $this->addSql("ALTER TABLE `activities_panelist` ADD FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`);");
    }
}
