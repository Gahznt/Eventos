<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412192538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `edition_payment_mode` ADD `has_free_individual_association` TINYINT  NULL  DEFAULT '1'  AFTER `initials`;");
        $this->addSql("ALTER TABLE `edition_signup` ADD `want_free_individual_association` TINYINT  NULL  DEFAULT '1'  AFTER `uploaded_file_path`;");
        $this->addSql("ALTER TABLE `edition_signup` ADD `free_individual_association_division_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `want_free_individual_association`;");
        $this->addSql("ALTER TABLE `edition_signup` ADD CONSTRAINT `edition_sign_up_free_individual_association_division_id` FOREIGN KEY (`free_individual_association_division_id`) REFERENCES `division` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
