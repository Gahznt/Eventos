<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210401191535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `system_evaluation` ADD `is_author_rated` TINYINT  NULL  DEFAULT '0'  AFTER `reject_justification`;");

        $this->addSql("ALTER TABLE `system_evaluation` ADD `author_rate_one` VARCHAR(10)  NULL  DEFAULT NULL  AFTER `is_author_rated`;");
        $this->addSql("ALTER TABLE `system_evaluation` ADD `author_rate_two` VARCHAR(10)  NULL  DEFAULT NULL  AFTER `author_rate_one`;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
