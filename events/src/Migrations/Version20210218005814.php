<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210218005814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `user_articles_authors` ADD `state_first_id` int(10) unsigned DEFAULT NULL  AFTER `order_author`;");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD `state_second_id` int(10) unsigned DEFAULT NULL  AFTER `state_first_id`;");

        $this->addSql("ALTER TABLE `user_articles_authors` ADD `institution_first_id` int(10) unsigned DEFAULT NULL  AFTER `state_second_id`;");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD `other_institution_first` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `institution_first_id`;");

        $this->addSql("ALTER TABLE `user_articles_authors` ADD `institution_second_id` int(10) unsigned DEFAULT NULL  AFTER `other_institution_first`;");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD `other_institution_second` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `institution_second_id`;");

        $this->addSql("ALTER TABLE `user_articles_authors` ADD `program_first_id` int(10) unsigned DEFAULT NULL  AFTER `other_institution_second`;");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD `other_program_first` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `program_first_id`;");

        $this->addSql("ALTER TABLE `user_articles_authors` ADD `program_second_id` int(10) unsigned DEFAULT NULL AFTER `other_program_first`;");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD `other_program_second` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `program_second_id`;");

        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_state_first_id FOREIGN KEY (state_first_id) REFERENCES state (id);");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_state_second_id FOREIGN KEY (state_second_id) REFERENCES state (id);");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_institution_first_id FOREIGN KEY (institution_first_id) REFERENCES institution (id);");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_institution_second_id FOREIGN KEY (institution_second_id) REFERENCES institution (id);");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_program_first_id FOREIGN KEY (program_first_id) REFERENCES program (id);");
        $this->addSql("ALTER TABLE `user_articles_authors` ADD CONSTRAINT user_articles_authors_program_second_id FOREIGN KEY (program_second_id) REFERENCES program (id);");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
