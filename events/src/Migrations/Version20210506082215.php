<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506082215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE program ADD COLUMN phone BIGINT(20) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN cellphone BIGINT(20) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN email VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN website VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN street VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN zipcode VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN number INT(11) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN complement VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN neighborhood VARCHAR(255) NULL");
        $this->addSql("ALTER TABLE program ADD COLUMN coordinator VARCHAR(255) NULL");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
