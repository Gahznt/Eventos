<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625200030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A94142436;");
        $this->addSql("DROP INDEX user_themes_id ON certificate;");
        $this->addSql("ALTER TABLE certificate DROP user_themes_id;");

        $this->addSql("CREATE TABLE certificates_user_themes (certificate_id BIGINT UNSIGNED NOT NULL, user_themes_id BIGINT UNSIGNED NOT NULL, INDEX IDX_1FF92AC799223FFD (certificate_id), INDEX IDX_1FF92AC794142436 (user_themes_id), PRIMARY KEY(certificate_id, user_themes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;");
        $this->addSql("ALTER TABLE certificates_user_themes ADD CONSTRAINT FK_1FF92AC799223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);");
        $this->addSql("ALTER TABLE certificates_user_themes ADD CONSTRAINT FK_1FF92AC794142436 FOREIGN KEY (user_themes_id) REFERENCES user_themes (id);");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
