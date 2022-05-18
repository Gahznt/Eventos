<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201320194214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_articles (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,user_id bigint(20) unsigned NOT NULL,season_id int(10) unsigned NOT NULL,division_id int(10) unsigned NOT NULL,theme_id int(10) unsigned NOT NULL,method_id int(10) unsigned NOT NULL,
          theory_id int(10) unsigned NOT NULL,
          last_id bigint(20) unsigned DEFAULT NULL,
          title varchar(255) NOT NULL,
          portuguese tinyint(1) DEFAULT 0,
          english tinyint(1) DEFAULT 0,
          spanish tinyint(1) DEFAULT 0,
          job_complete tinyint(1) DEFAULT 0,
          resume_flag tinyint(1) DEFAULT 0,
          rac_bar tinyint(1) DEFAULT 0,
          never_publish tinyint(1) DEFAULT 0,
          resume text,
          confirm_files_correct tinyint(1) DEFAULT 0,
          acknowledgment text,
          language smallint(5) DEFAULT NULL,
          frame smallint(5) DEFAULT NULL,
          premium tinyint(1) DEFAULT 0,
          PRIMARY KEY (id),
          KEY user_id (user_id),
          KEY season_id (season_id),
          KEY division_id (division_id),
          KEY theme_id (theme_id),
          KEY method_id (method_id),
          KEY theory_id (theory_id),
          KEY last_id (last_id),
          CONSTRAINT user_articles_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id),
          CONSTRAINT user_articles_ibfk_2 FOREIGN KEY (season_id) REFERENCES season (id),
          CONSTRAINT user_articles_ibfk_3 FOREIGN KEY (division_id) REFERENCES division (id),
          CONSTRAINT user_articles_ibfk_4 FOREIGN KEY (theme_id) REFERENCES theme (id),
          CONSTRAINT user_articles_ibfk_5 FOREIGN KEY (method_id) REFERENCES method (id),
          CONSTRAINT user_articles_ibfk_6 FOREIGN KEY (theory_id) REFERENCES theory (id),
          CONSTRAINT user_articles_ibfk_7 FOREIGN KEY (last_id) REFERENCES user_articles (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_articles');
    }
}
