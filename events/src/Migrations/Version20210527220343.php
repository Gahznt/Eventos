<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527220343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `event` ADD `is_show_previous_events_home` TINYINT(1)  NULL  DEFAULT '1'  AFTER `position`;");
        $this->addSql("ALTER TABLE `edition` ADD `is_show_home` TINYINT(1)  NULL  DEFAULT '1'  AFTER `is_homolog`;");
        $this->addSql("ALTER TABLE `edition` ADD `home_position` INT  NULL  DEFAULT 0  AFTER `is_show_home`;");
        $this->addSql("ALTER TABLE `event` DROP `position`;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
