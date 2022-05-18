<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409140034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `edition_discount` ADD `type` SMALLINT  NULL  DEFAULT '99'  AFTER `percentage`;");

        $this->addSql("ALTER TABLE `edition_payment_mode` CHANGE `type` `type` SMALLINT(5)  NULL  DEFAULT '3';");

        $this->addSql("ALTER TABLE `edition_signup` ADD `uploaded_file_name` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `status_pay`;");
        $this->addSql("ALTER TABLE `edition_signup` ADD `uploaded_file_path` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `uploaded_file_name`;");


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
