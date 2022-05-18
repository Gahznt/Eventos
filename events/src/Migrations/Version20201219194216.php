<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201219194216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_association (id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT, type tinyint(2) DEFAULT NULL, user_id bigint(20) unsigned DEFAULT NULL, institution_id int(10) unsigned DEFAULT NULL, program_id int(10) unsigned DEFAULT NULL, division_id int(10) unsigned DEFAULT NULL, updated_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, last_pay DATETIME DEFAULT NULL, PRIMARY KEY (id), KEY fk_ua_user_idx (user_id), KEY fk_ua_instit_idx (institution_id), KEY fk_ua_prog_idx (program_id), KEY fk_ua_division_idx (division_id), CONSTRAINT fk_ua_division FOREIGN KEY (division_id) REFERENCES division (id) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT fk_ua_instit FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT fk_ua_prog FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT fk_ua_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->addSql('CREATE TABLE user_association_divisions (id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, user_association_id BIGINT(20) UNSIGNED NULL, division_id INT UNSIGNED NULL, PRIMARY KEY (id), INDEX fk_uad_ua_idx (user_association_id ASC), INDEX fk_uad_div_idx (division_id ASC), CONSTRAINT fk_uad_ua FOREIGN KEY (user_association_id) REFERENCES anpad_events.user_association (id) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT fk_uad_div FOREIGN KEY (division_id) REFERENCES anpad_events.division (id) ON DELETE NO ACTION ON UPDATE NO ACTION)');

    }

    public function down(Schema $schema) : void
    {
    }
}
