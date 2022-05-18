<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126194707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `program` ADD `status` SMALLINT(5)  NULL  DEFAULT '1'  AFTER `paid`;");
        $this->addSql("ALTER TABLE `program` ADD `institution_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `id`;");
        $this->addSql("ALTER TABLE `program` ADD CONSTRAINT `program_institution_id` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        $this->addSql("ALTER TABLE `institution` ADD `initials` VARCHAR(50)  NULL  DEFAULT NULL  AFTER `name`;");


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
