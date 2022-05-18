<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201403175847 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE theme ADD division_id INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER season_id');
        $this->addSql('ALTER TABLE theme ADD FOREIGN KEY (division_id) REFERENCES division (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY theme_ibfk_2');
        $this->addSql('ALTER TABLE theme DROP division_id');
    }
}
