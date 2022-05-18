<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407205751 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE `edition_payment_mode` CHANGE `is_associated` `type` SMALLINT(5)  NULL  DEFAULT '0';");

        $this->addSql("UPDATE edition_payment_mode SET type = 2 WHERE TYPE = 1;");
        $this->addSql("UPDATE edition_payment_mode SET type = 1 WHERE TYPE = 0;");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
