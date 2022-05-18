<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506150710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `event` ADD `issn` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL after `number_words`;");
        $this->addSql("ALTER TABLE `edition` ADD `longname_portuguese` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `name_portuguese`;");
        $this->addSql("ALTER TABLE `edition` ADD `longname_english` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `name_english`;");
        $this->addSql("ALTER TABLE `edition` ADD `longname_spanish` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `name_spanish`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
