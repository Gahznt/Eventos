<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210917191855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE edition 
            ADD certificate_qrcode_size INT DEFAULT NULL, 
            ADD certificate_qrcode_position_right INT DEFAULT NULL, 
            ADD certificate_qrcode_position_bottom INT DEFAULT NULL, 
            CHANGE color color VARCHAR(50) NOT NULL, 
            CHANGE place place VARCHAR(255) NOT NULL, 
            CHANGE name_portuguese name_portuguese VARCHAR(255) NOT NULL, 
            CHANGE name_english name_english VARCHAR(255) NOT NULL, 
            CHANGE name_spanish name_spanish VARCHAR(255) NOT NULL, 
            CHANGE status status SMALLINT DEFAULT NULL, 
            CHANGE is_homolog is_homolog TINYINT(1) DEFAULT NULL, 
            CHANGE home_position home_position INT DEFAULT NULL, 
            CHANGE cartificate_layout_path certificate_layout_path VARCHAR(255) DEFAULT NULL;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
