<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209230000007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `certificate` ADD `view_params` text NULL AFTER `manual`;");
        
        $this->addSql("UPDATE `certificate` SET `manual` = TRUE 
            WHERE `short` IN ('PART_CIENT', 'PART_DIV', 'COM_CIENT', 'COORD_DIV', 'PREM', 'PART');");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","activity_title","user_name"]' . "' WHERE `short` = 'PART_CIENT';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","activity_title","user_name"]' . "' WHERE `short` = 'PART_DIV';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","article_id","article_title","user_name"]' . "' WHERE `short` = 'APRES';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_name","user_theme_title","articles_count"]' . "' WHERE `short` = 'AVAL';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","division_name"]' . "' WHERE `short` = 'COM_CIENT';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","division_name"]' . "' WHERE `short` = 'COORD_DIV';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","user_theme_title"]' . "' WHERE `short` = 'COORD_SES';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","user_theme_title"]' . "' WHERE `short` = 'COORD_DEB';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","user_theme_title"]' . "' WHERE `short` = 'DEB_SESS';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","division_id","division_name","article_id","article_title","user_name"]' . "' WHERE `short` = 'PREM';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","workload_hours"]' . "' WHERE `short` = 'PART';");

        $this->addSql("UPDATE `certificate` SET `view_params` = '" . '["edition_name","user_name","division_id","division_name","user_theme_title"]' . "' WHERE `short` = 'LIDER';");

        $this->addSql("UPDATE `certificate` SET short='AVAL_THEME', name='Avaliação de Temas' WHERE `short` = 'AVAL';");

        $this->addSql("INSERT INTO `certificate` (short, name, title, auto, manual, view_params, updated_at) VALUES 	
            ('AVAL_PANEL', 'Avaliação de Painéis', 'CERTIFICADO DE AVALIAÇÃO', 1, 0, '" .'["edition_name","user_name","division_name","panel_title","articles_count"]' . "', now());");

        $this->addSql("UPDATE `certificate` SET `short` = 'AVAL_THEME' WHERE `short` = 'AVAL';");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `certificate`;");
    }
}
