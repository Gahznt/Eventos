<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330130706 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("alter table program add created_at datetime null;");
        $this->addSql("alter table program add updated_at datetime null;");
        $this->addSql("alter table program add deleted_at datetime null;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql("alter table program drop column created_at;");
        $this->addSql("alter table program drop column updated_at;");
        $this->addSql("alter table program drop column deleted_at;");
    }
}
