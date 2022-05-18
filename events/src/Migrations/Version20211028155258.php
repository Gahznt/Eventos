<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028155258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_academics DROP FOREIGN KEY user_academics_ibfk_4');
        $this->addSql('ALTER TABLE user_academics DROP FOREIGN KEY user_academics_ibfk_5');
        $this->addSql('DROP INDEX institution_id ON user_academics');
        $this->addSql('DROP INDEX program_id ON user_academics');
        $this->addSql('CALL rename_index(\'user_academics\', \'id_user\', \'IDX_15759E46A76ED395\')');
        $this->addSql('DROP INDEX last_id ON user_articles');
        $this->addSql('CALL rename_index(\'user_articles\', \'user_id\', \'IDX_5F50D568A76ED395\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'user_articles_ibfk_9\', \'IDX_5F50D56838FAD351\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'edition_id\', \'IDX_5F50D56874281A5E\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'division_id\', \'IDX_5F50D56841859289\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'theme_id\', \'IDX_5F50D56894142436\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'original_user_themes_id\', \'IDX_5F50D5686F0F098A\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'method_id\', \'IDX_5F50D56819883967\')');
        $this->addSql('CALL rename_index(\'user_articles\', \'theory_id\', \'IDX_5F50D5686441A32F\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_id\', \'IDX_B67C0C2DB7A13F59\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_author_id\', \'IDX_B67C0C2DF6957EFF\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_institution_first_id\', \'IDX_B67C0C2DF6286EC1\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_institution_second_id\', \'IDX_B67C0C2DE83FC083\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_program_first_id\', \'IDX_B67C0C2D69789E6A\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_program_second_id\', \'IDX_B67C0C2DA9A4EA13\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_state_first_id\', \'IDX_B67C0C2DD7BE04F8\')');
        $this->addSql('CALL rename_index(\'user_articles_authors\', \'user_articles_authors_state_second_id\', \'IDX_B67C0C2DB71BDEE1\')');
        $this->addSql('CALL rename_index(\'user_articles_files\', \'user_articles_id\', \'IDX_8552B7F4B7A13F59\')');
        $this->addSql('CALL rename_index(\'user_articles_keywords\', \'keyword_id\', \'IDX_F3D4F86F115D4552\')');
        $this->addSql('CALL rename_index(\'user_articles_keywords\', \'user_articles_id\', \'IDX_F3D4F86FB7A13F59\')');
        $this->addSql('CALL rename_index(\'user_association\', \'fk_ua_user_idx\', \'IDX_549EE859A76ED395\')');
        $this->addSql('CALL rename_index(\'user_association\', \'fk_ua_instit_idx\', \'IDX_549EE85910405986\')');
        $this->addSql('CALL rename_index(\'user_association\', \'fk_ua_prog_idx\', \'IDX_549EE8593EB8070A\')');
        $this->addSql('CALL rename_index(\'user_association\', \'fk_ua_division_idx\', \'IDX_549EE85941859289\')');
        $this->addSql('CALL rename_index(\'user_association_divisions\', \'fk_uad_ua_idx\', \'IDX_6527483A843C17FC\')');
        $this->addSql('CALL rename_index(\'user_association_divisions\', \'fk_uad_div_idx\', \'IDX_6527483A41859289\')');
        $this->addSql('CALL rename_index(\'user_committee\', \'coordinator_id\', \'IDX_D2124FD3A76ED395\')');
        $this->addSql('CALL rename_index(\'user_committee\', \'edition_id\', \'IDX_D2124FD374281A5E\')');
        $this->addSql('CALL rename_index(\'user_committee\', \'division_id\', \'IDX_D2124FD341859289\')');
        $this->addSql('CALL rename_index(\'user_consents\', \'user_id\', \'IDX_E6572967A76ED395\')');
        $this->addSql('DROP INDEX user_id ON user_evaluation_articles');
        $this->addSql('CALL rename_index(\'user_evaluation_articles\', \'division_first_id\', \'IDX_9850273CB5565F6D\')');
        $this->addSql('CALL rename_index(\'user_evaluation_articles\', \'division_second_id\', \'IDX_9850273C371C5171\')');
        $this->addSql('CALL rename_index(\'user_evaluation_articles\', \'theme_first_id\', \'IDX_9850273C4A737028\')');
        $this->addSql('CALL rename_index(\'user_evaluation_articles\', \'theme_second_id\', \'IDX_9850273C3155C141\')');
        $this->addSql('CALL rename_index(\'user_evaluation_articles\', \'uniq_5f50d568a76ed395\', \'UNIQ_9850273CA76ED395\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'state_first_id\', \'IDX_5D45554AD7BE04F8\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'state_second_id\', \'IDX_5D45554AB71BDEE1\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'institution_first_id\', \'IDX_5D45554AF6286EC1\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'institution_second_id\', \'IDX_5D45554AE83FC083\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'program_first_id\', \'IDX_5D45554A69789E6A\')');
        $this->addSql('CALL rename_index(\'user_institutions_programs\', \'program_second_id\', \'IDX_5D45554AA9A4EA13\')');
        $this->addSql('CALL rename_index(\'user_theme_keyword\', \'fk_utk_keyw_idx\', \'IDX_ED7ED948115D4552\')');
        $this->addSql('CALL rename_index(\'user_theme_keyword\', \'id_user\', \'IDX_ED7ED948A76ED395\')');
        $this->addSql('CALL rename_index(\'user_themes\', \'user_id\', \'IDX_701029E3A76ED395\')');
        $this->addSql('CALL rename_index(\'user_themes\', \'division_id\', \'IDX_701029E341859289\')');
        $this->addSql('CALL rename_index(\'user_themes_bibliographies\', \'user_themes_id\', \'IDX_C49293FD94142436\')');
        $this->addSql('ALTER TABLE user_themes_details DROP INDEX user_themes_id, ADD UNIQUE INDEX UNIQ_AB10566E94142436 (user_themes_id)');
        $this->addSql('CALL rename_index(\'user_themes_evaluation_log\', \'user_themes_id\', \'IDX_49D3AABD94142436\')');
        $this->addSql('CALL rename_index(\'user_themes_evaluation_log\', \'user_themes_evaluation_log_ibfk_2\', \'IDX_49D3AABDA76ED395\')');
        $this->addSql('CALL rename_index(\'user_themes_researchers\', \'user_themes_id\', \'IDX_317A5F7F94142436\')');
        $this->addSql('CALL rename_index(\'user_themes_researchers\', \'researcher_id\', \'IDX_317A5F7FC7533BDE\')');
        $this->addSql('CALL rename_index(\'user_themes_reviewers\', \'user_themes_id\', \'IDX_12FF7A1794142436\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
