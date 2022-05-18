<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201635194217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `user` CHANGE `extension` `extension` INT(11)  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `complement` `complement` VARCHAR(255)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `street` `street` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `zipcode` `zipcode` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `password` `password` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `email` `email` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `nickname` `nickname` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `identifier` `identifier` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
            ALTER TABLE `user` CHANGE `neighborhood` `neighborhood` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
