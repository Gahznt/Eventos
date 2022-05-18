<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204800250668 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_evaluation` DROP `criteria_eleven`;");
        $this->addSql("ALTER TABLE `system_evaluation` ADD `criteria_final` TINYINT(2)  NULL  DEFAULT '1'  AFTER `criteria_ten`;");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_one` `criteria_one` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_two` `criteria_two` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_three` `criteria_three` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_four` `criteria_four` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_five` `criteria_five` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_six` `criteria_six` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_seven` `criteria_seven` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_eight` `criteria_eight` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_nine` `criteria_nine` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_ten` `criteria_ten` VARCHAR(10)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT 'weak';");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `system_evaluation` DROP `criteria_eleven`;");
        $this->addSql("ALTER TABLE `system_evaluation` ADD `criteria_eleven` TINYINT(2)  NULL  DEFAULT '1'  AFTER `criteria_ten`;");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_one` `criteria_one` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_two` `criteria_two` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_three` `criteria_three` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_four` `criteria_four` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_five` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_six` `criteria_six` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_seven` `criteria_seven` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_eight` `criteria_eight` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_nine` `criteria_nine` TINYINT(2)  NULL  DEFAULT '1'");
        $this->addSql("ALTER TABLE `system_evaluation` CHANGE `criteria_ten` `criteria_ten` TINYINT(2)  NULL  DEFAULT '1'");
    }
}
