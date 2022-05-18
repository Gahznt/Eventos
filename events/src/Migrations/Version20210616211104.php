<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210616211104 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `edition` ADD `cartificate_layout_path` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `home_position`;");

        $this->addSql("ALTER TABLE certificate  
                ADD activity_id BIGINT UNSIGNED DEFAULT NULL after html, 
                ADD panel_id BIGINT UNSIGNED DEFAULT NULL after activity_id, 
                ADD user_themes_id BIGINT UNSIGNED DEFAULT NULL after panel_id, 
                ADD thesis_id BIGINT UNSIGNED DEFAULT NULL after user_themes_id, 
                ADD division_id INT UNSIGNED DEFAULT NULL after thesis_id, 
                CHANGE is_active is_active TINYINT(1) DEFAULT '0';
                
                 ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id);
                 ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A6F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id);
                 ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A94142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id);
                 ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A68D82738 FOREIGN KEY (thesis_id) REFERENCES thesis (id);
                 ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A41859289 FOREIGN KEY (division_id) REFERENCES division (id);
                
                 CREATE INDEX activity_id ON certificate (activity_id);
                 CREATE INDEX panel_id ON certificate (panel_id);
                 CREATE INDEX user_themes_id ON certificate (user_themes_id);
                 CREATE INDEX thesis_id ON certificate (thesis_id);
                 CREATE INDEX division_id ON certificate (division_id);");

        $this->addSql("CREATE TABLE certificates_user_articles (certificate_id BIGINT UNSIGNED NOT NULL, user_articles_id BIGINT UNSIGNED NOT NULL, INDEX IDX_37B10A9D99223FFD (certificate_id), INDEX IDX_37B10A9DB7A13F59 (user_articles_id), PRIMARY KEY(certificate_id, user_articles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
                ALTER TABLE certificates_user_articles ADD CONSTRAINT FK_37B10A9D99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);
                ALTER TABLE certificates_user_articles ADD CONSTRAINT FK_37B10A9DB7A13F59 FOREIGN KEY (user_articles_id) REFERENCES user_articles (id);");

        $this->addSql("ALTER TABLE user_articles_authors ADD is_presented TINYINT(1) DEFAULT '0';");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
