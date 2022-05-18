<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20209230000004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `certificate` (short, name, title, updated_at) VALUES 	
            ('PART_CIENT', 'Atividade Científica', 'CERTIFICADO DE PARTICIPAÇÃO DE ATIVIDADE CIENTÍFICA', now()),
            ('PART_DIV', 'Atividade Divisional', 'CERTIFICADO DE PARTICIPAÇÃO DE ATIVIDADE DIVISIONAL', now()),
            ('APRES', 'Apresentação de Trabalhos', 'CERTIFICADO DE APRESENTAÇÃO', now()),
            ('AVAL', 'Avaliação de Trabalhos', 'CERTIFICADO DE AVALIAÇÃO', now()),
            ('COM_CIENT', 'Comitês Científicos', 'CERTIFICADO DE COMITÊ CIENTÍFICO', now()),
            ('COORD_DIV', 'Coordenador de Divisão', 'CERTIFICADO DE COORDENADOR DE DIVISÃO', now()),
            ('COORD_SES', 'Coordenador de Sessão', 'CERTIFICADO DE COORDENADOR DE SESSÃO', now()),
            ('COORD_DEB', 'Coordenador / Debatedor', 'CERTIFICADO DE COORDENADOR/DEBATEDOR DE SESSÃO', now()),
            ('DEB_SESS', 'Debatedor de Sessão', 'CERTIFICADO DE DEBATEDOR DE SESSÃO', now()),
            ('PREM', 'Indicados Prêmio', 'DECLARAÇÃO DE INDICAÇÃO A PRÊMIO', now()),
            ('PART', 'Participante', 'CERTIFICADO DE PARTICIPAÇÃO', now()),
            ('LIDER', 'Líder de Tema', 'CERTIFICADO DE LÍDER DE TEMA', now());");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `certificate`;");
    }
}
