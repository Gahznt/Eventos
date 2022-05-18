<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210430183942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `edition_signup` ADD `free_individual_association_user_association_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL;");
        $this->addSql("ALTER TABLE `edition_signup` ADD CONSTRAINT `free_individual_association_user_association_id` FOREIGN KEY (`free_individual_association_user_association_id`) REFERENCES `user_association` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
