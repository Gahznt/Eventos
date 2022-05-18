<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825203243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `user` SET `birthday` = NULL WHERE `birthday` LIKE \'0000-00-0%\'');
        $this->addSql('UPDATE `user` SET `created_at` = NULL WHERE `created_at` LIKE \'0000-00-0%\'');
        $this->addSql('ALTER TABLE user ADD locale VARCHAR(255) DEFAULT NULL, ADD is_foreign_use_cpf TINYINT(1) DEFAULT NULL, ADD is_foreign_use_passport SMALLINT DEFAULT NULL, CHANGE number number VARCHAR(255) DEFAULT NULL, CHANGE record_type record_type SMALLINT DEFAULT NULL, CHANGE payment payment SMALLINT DEFAULT NULL, CHANGE level level SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP locale, DROP is_foreign_use_cpf, DROP is_foreign_use_passport, CHANGE number number INT DEFAULT NULL, CHANGE record_type record_type TINYINT(1) DEFAULT NULL COMMENT \'perfil em código\', CHANGE payment payment TINYINT(1) DEFAULT NULL, CHANGE level level TINYINT(1) DEFAULT NULL');
    }
}
