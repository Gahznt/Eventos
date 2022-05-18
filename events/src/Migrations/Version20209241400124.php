<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209241400124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("update user_evaluation_articles set division_second_id=3 where division_second_id=13;");
        $this->addSql("update user_evaluation_articles set division_first_id=3 where division_first_id=13;");
        $this->addSql("update keyword set division_id=3 where division_id=13;");
        $this->addSql("update system_evaluation_averages set division_id=3 where division_id=13;");
        $this->addSql("update user_articles set division_id=3 where division_id=13;");
        $this->addSql("delete from division where id=13;");

        $this->addSql("ALTER TABLE `institution` ADD `sort_position` INT  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `coordinator`;");
        $this->addSql("ALTER TABLE `program` ADD `sort_position` INT  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `paid`;");

        $this->addSql("INSERT INTO institution (id, name, sort_position)
            SELECT * FROM (SELECT '99999', 'Outra', '999') AS tmp
            WHERE NOT EXISTS (
                SELECT name FROM institution WHERE name = 'Outra'
            ) LIMIT 1;");

        $this->addSql("INSERT INTO program (id, name, sort_position)
            SELECT * FROM (SELECT '99999', 'Outro', '999') AS tmp
            WHERE NOT EXISTS (
                SELECT name FROM program WHERE name = 'Outro'
            ) LIMIT 1;");

        $this->addSql("ALTER TABLE `user_academics` ADD `other_institution` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `institution_id`;");

        $this->addSql("ALTER TABLE `user_institutions_programs` ADD `other_institution_first` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `institution_first_id`;");
        $this->addSql("ALTER TABLE `user_institutions_programs` ADD `other_institution_second` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `institution_second_id`;");
        $this->addSql("ALTER TABLE `user_institutions_programs` ADD `other_program_first` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `program_first_id`;");
        $this->addSql("ALTER TABLE `user_institutions_programs` ADD `other_program_second` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `program_second_id`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
