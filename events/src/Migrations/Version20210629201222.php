<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629201222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A41859289;");
        $this->addSql("ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A68D82738;");
        $this->addSql("ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A6F6FCB26;");
        $this->addSql("ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A81C06096;");
        $this->addSql("DROP INDEX division_id ON certificate;");
        $this->addSql("DROP INDEX panel_id ON certificate;");
        $this->addSql("DROP INDEX activity_id ON certificate;");
        $this->addSql("DROP INDEX thesis_id ON certificate;");
        $this->addSql("ALTER TABLE certificate DROP activity_id, DROP panel_id, DROP thesis_id, DROP division_id;");


        $this->addSql("CREATE TABLE certificates_activities (certificate_id BIGINT UNSIGNED NOT NULL, activity_id BIGINT UNSIGNED NOT NULL, INDEX IDX_EC856A3399223FFD (certificate_id), INDEX IDX_EC856A3381C06096 (activity_id), PRIMARY KEY(certificate_id, activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE certificates_panels (certificate_id BIGINT UNSIGNED NOT NULL, panel_id BIGINT UNSIGNED NOT NULL, INDEX IDX_895A5C0C99223FFD (certificate_id), INDEX IDX_895A5C0C6F6FCB26 (panel_id), PRIMARY KEY(certificate_id, panel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE certificates_theses (certificate_id BIGINT UNSIGNED NOT NULL, thesis_id BIGINT UNSIGNED NOT NULL, INDEX IDX_1B39FE199223FFD (certificate_id), INDEX IDX_1B39FE168D82738 (thesis_id), PRIMARY KEY(certificate_id, thesis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE certificates_divisions (certificate_id BIGINT UNSIGNED NOT NULL, division_id INT UNSIGNED NOT NULL, INDEX IDX_C63CCD4D99223FFD (certificate_id), INDEX IDX_C63CCD4D41859289 (division_id), PRIMARY KEY(certificate_id, division_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;");
        $this->addSql("ALTER TABLE certificates_activities ADD CONSTRAINT FK_EC856A3399223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);");
        $this->addSql("ALTER TABLE certificates_activities ADD CONSTRAINT FK_EC856A3381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id);");
        $this->addSql("ALTER TABLE certificates_panels ADD CONSTRAINT FK_895A5C0C99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);");
        $this->addSql("ALTER TABLE certificates_panels ADD CONSTRAINT FK_895A5C0C6F6FCB26 FOREIGN KEY (panel_id) REFERENCES panel (id);");
        $this->addSql("ALTER TABLE certificates_theses ADD CONSTRAINT FK_1B39FE199223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);");
        $this->addSql("ALTER TABLE certificates_theses ADD CONSTRAINT FK_1B39FE168D82738 FOREIGN KEY (thesis_id) REFERENCES thesis (id);");
        $this->addSql("ALTER TABLE certificates_divisions ADD CONSTRAINT FK_C63CCD4D99223FFD FOREIGN KEY (certificate_id) REFERENCES certificate (id);");
        $this->addSql("ALTER TABLE certificates_divisions ADD CONSTRAINT FK_C63CCD4D41859289 FOREIGN KEY (division_id) REFERENCES division (id);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
