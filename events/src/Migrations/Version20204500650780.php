<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20204500650780 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // FK Theme
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D5684A737028`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D5683155C141`;");

        // FK Keywords
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D5683EE225E8`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D5687926AF0E`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568B63A3001`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568E36B1163`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568E6CAC034`;");
        $this->addSql("ALTER TABLE `user_evaluation_articles` DROP FOREIGN KEY `FK_5F50D568EA0DC6EA`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
