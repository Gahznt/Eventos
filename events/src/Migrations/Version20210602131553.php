<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210602131553 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
            create table certificate
            (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                short varchar(255) null,
                name varchar(255) null,
                title varchar(255) null,
                auto boolean null,
                manual boolean null,
                view_params text null,
                updated_at datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                created_at datetime null,
                PRIMARY KEY (`id`)
            );
        ");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
