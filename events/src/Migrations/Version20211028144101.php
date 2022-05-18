<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028144101 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CALL rename_index(\'activities_guest\', \'activities_guest_guest_id\', \'IDX_6F3EB9E69A4AA658\')');
        $this->addSql('CALL rename_index(\'activities_guest\', \'activities_guest_activity_id\', \'IDX_6F3EB9E681C06096\')');
        $this->addSql('CALL rename_index(\'activities_panelist\', \'activities_panelist_activity_id\', \'IDX_47117FF481C06096\')');
        $this->addSql('CALL rename_index(\'activities_panelist\', \'activities_panelist_panelist_id\', \'IDX_47117FF47E8B14D\')');
        $this->addSql('CALL rename_index(\'activity\', \'event_id\', \'IDX_AC74095A74281A5E\')');
        $this->addSql('CALL rename_index(\'activity\', \'division_id\', \'IDX_AC74095A41859289\')');
        $this->addSql('CALL rename_index(\'certificate\', \'edition_id\', \'IDX_219CDA4A74281A5E\')');
        $this->addSql('CALL rename_index(\'certificate\', \'user_id\', \'IDX_219CDA4AA76ED395\')');
        $this->addSql('CALL rename_index(\'city\', \'cities_test_ibfk_1\', \'IDX_2D5B02345D83CC1\')');
        $this->addSql('CALL rename_index(\'city\', \'cities_test_ibfk_2\', \'IDX_2D5B0234F92F3E70\')');
        $this->addSql('CALL rename_index(\'course\', \'id_institution\', \'IDX_169E6FB910405986\')');
        $this->addSql('CALL rename_index(\'course\', \'id_program\', \'IDX_169E6FB93EB8070A\')');
        $this->addSql('CALL rename_index(\'division_coordinator\', \'coordinator_id\', \'IDX_D29CBE9BE7877946\')');
        $this->addSql('CALL rename_index(\'division_coordinator\', \'division_id\', \'IDX_D29CBE9B41859289\')');
        $this->addSql('CALL rename_index(\'division_coordinator\', \'edition_id\', \'IDX_D29CBE9B74281A5E\')');
        $this->addSql('CALL rename_index(\'edition\', \'edition_event_id\', \'IDX_A891181F71F7E88B\')');
        $this->addSql('CALL rename_index(\'edition_discount\', \'edition_discount_edition_id\', \'IDX_AFF7B85B74281A5E\')');
        $this->addSql('CALL rename_index(\'edition_file\', \'edition_id\', \'IDX_3CA8DD0E74281A5E\')');
        $this->addSql('CALL rename_index(\'edition_payment_mode\', \'edition_payment_mode_edition_id\', \'IDX_D7673E2A74281A5E\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'edition_sign_up_free_individual_association_division_id\', \'IDX_AD3AAAF730576121\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'edition_signup_edition_discount_id\', \'IDX_AD3AAAF796D47C1\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'joined_id\', \'IDX_AD3AAAF776C94ED4\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'event_payment_mode_id\', \'IDX_AD3AAAF7C188E957\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'edition_id\', \'IDX_AD3AAAF774281A5E\')');
        $this->addSql('CALL rename_index(\'edition_signup\', \'free_individual_association_user_association_id\', \'IDX_AD3AAAF73346BFDA\')');
        $this->addSql('CALL rename_index(\'edition_signup_articles\', \'event_signup_id\', \'IDX_F196D933442E2D19\')');
        $this->addSql('CALL rename_index(\'edition_signup_articles\', \'user_article_id\', \'IDX_F196D933B7A13F59\')');
        $this->addSql('CALL rename_index(\'event_divisions\', \'event_divisions_event_id\', \'IDX_8751320C71F7E88B\')');
        $this->addSql('CALL rename_index(\'event_divisions\', \'event_divisions_division_id\', \'IDX_8751320C41859289\')');
        $this->addSql('CALL rename_index(\'institution\', \'fk_inst_city_idx\', \'IDX_3A9F98E58BAC62AF\')');
        $this->addSql('CALL rename_index(\'keyword\', \'fk_key_divi_idx\', \'IDX_5A93713B41859289\')');
        $this->addSql('CALL rename_index(\'keyword\', \'fk_key_them_idx\', \'IDX_5A93713B59027487\')');
        $this->addSql('CALL rename_index(\'panel\', \'division_id\', \'IDX_A2ADD30F41859289\')');
        $this->addSql('CALL rename_index(\'panel\', \'edition_id\', \'IDX_A2ADD30F74281A5E\')');
        $this->addSql('CALL rename_index(\'panel\', \'proponent_id\', \'IDX_A2ADD30FF8BAC902\')');
        $this->addSql('CALL rename_index(\'panel_evaluation_log\', \'panel_id\', \'IDX_EB0C33086F6FCB26\')');
        $this->addSql('CALL rename_index(\'panel_evaluation_log\', \'panel_evaluation_log_ibfk_2\', \'IDX_EB0C3308A76ED395\')');
        $this->addSql('CALL rename_index(\'panels_panelist\', \'panel_id\', \'IDX_E9D3A9F36F6FCB26\')');
        $this->addSql('CALL rename_index(\'panels_panelist\', \'panelist_id\', \'IDX_E9D3A9F37E8B14D\')');
        $this->addSql('CALL rename_index(\'payment_user_association\', \'payment_user_id\', \'IDX_1B4692E7A76ED395\')');
        $this->addSql('CALL rename_index(\'payment_user_association_details\', \'payment_user_association_id\', \'IDX_391DEE7816240F33\')');
        $this->addSql('CALL rename_index(\'program\', \'program_institution_id\', \'IDX_92ED778410405986\')');
        $this->addSql('CALL rename_index(\'speaker\', \'edition_id\', \'IDX_7B85DB6174281A5E\')');
        $this->addSql('CALL rename_index(\'state\', \'country_region\', \'IDX_A393D2FBF92F3E70\')');
        $this->addSql('CALL rename_index(\'subsection\', \'subsection_edition\', \'IDX_6611D22074281A5E\')');
        $this->addSql('CALL rename_index(\'system_ensalement_rooms\', \'edition_id\', \'IDX_554F79BC74281A5E\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'divison_id\', \'IDX_B84B16FC41859289\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'edition_id\', \'IDX_B84B16FC74281A5E\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'panel_id\', \'IDX_B84B16FC6F6FCB26\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'activity_id\', \'IDX_B84B16FC81C06096\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'system_ensalement_slots_id\', \'IDX_B84B16FC2229B35F\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'system_ensalement_scheduling_session_id\', \'IDX_B84B16FC88E52D25\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'user_themes_id\', \'IDX_B84B16FC94142436\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'user_register_id\', \'IDX_B84B16FCE06D02EB\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'coordinator_id\', \'IDX_B84B16FCAE566ADF\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling\', \'debater_id\', \'IDX_B84B16FCBCE3C531\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling_articles\', \'system_ensalement_sheduling_id\', \'IDX_EB9ECCB1C58A82E5\')');
        $this->addSql('CALL rename_index(\'system_ensalement_scheduling_articles\', \'user_articles_id\', \'IDX_EB9ECCB1B7A13F59\')');
        $this->addSql('CALL rename_index(\'system_ensalement_sessions\', \'edition_id\', \'IDX_A6D8A91774281A5E\')');
        $this->addSql('CALL rename_index(\'system_ensalement_slots\', \'edition_id\', \'IDX_E19A56FA74281A5E\')');
        $this->addSql('CALL rename_index(\'system_ensalement_slots\', \'system_ensalement_sessions_id\', \'IDX_E19A56FA88E52D25\')');
        $this->addSql('CALL rename_index(\'system_ensalement_slots\', \'system_ensalement_rooms_id\', \'IDX_E19A56FAB29B5CAF\')');
        $this->addSql('CALL rename_index(\'system_evaluation\', \'user_articles_id\', \'IDX_A6F0B54CB7A13F59\')');
        $this->addSql('CALL rename_index(\'system_evaluation\', \'user_owner_id\', \'IDX_A6F0B54C9EB185F9\')');
        $this->addSql('CALL rename_index(\'system_evaluation_averages\', \'user_id\', \'IDX_113FE026A76ED395\')');
        $this->addSql('CALL rename_index(\'system_evaluation_averages\', \'edition_id\', \'IDX_113FE02674281A5E\')');
        $this->addSql('CALL rename_index(\'system_evaluation_averages\', \'division_id\', \'IDX_113FE02641859289\')');
        $this->addSql('CALL rename_index(\'system_evaluation_averages_articles\', \'user_articles_id\', \'IDX_7587AEC5B7A13F59\')');
        $this->addSql('CALL rename_index(\'system_evaluation_averages_articles\', \'system_evaluation_averages_id\', \'IDX_7587AEC5B29921A2\')');
        $this->addSql('CALL rename_index(\'system_evaluation_config\', \'user_id\', \'IDX_C42143B0A76ED395\')');
        $this->addSql('CREATE INDEX IDX_C19235A8B7A13F59 ON system_evaluation_indications (user_articles_id)');
        $this->addSql('CREATE INDEX IDX_C19235A8C39B27E0 ON system_evaluation_indications (user_evaluator_id)');
        $this->addSql('CALL rename_index(\'system_evaluation_log\', \'sytem_evaluation_id\', \'IDX_FB0A4D21E1E88338\')');
        $this->addSql('CALL rename_index(\'system_evaluation_log\', \'user_log_id\', \'IDX_FB0A4D218F3546BB\')');
        $this->addSql('CALL rename_index(\'theme\', \'edition_id\', \'IDX_9775E70874281A5E\')');
        $this->addSql('CALL rename_index(\'theme\', \'division_id\', \'IDX_9775E70841859289\')');
        $this->addSql('CALL rename_index(\'thesis\', \'division_id\', \'IDX_AF4FF3A841859289\')');
        $this->addSql('CALL rename_index(\'thesis\', \'edition_id\', \'IDX_AF4FF3A874281A5E\')');
        $this->addSql('CALL rename_index(\'thesis\', \'user_id\', \'IDX_AF4FF3A8A76ED395\')');
        $this->addSql('CALL rename_index(\'thesis\', \'user_themes_id\', \'IDX_AF4FF3A894142436\')');
        $this->addSql('CALL rename_index(\'user_methods\', \'id_user\', \'IDX_EEFE963A76ED395\')');
        $this->addSql('CALL rename_index(\'user_methods\', \'fk_um_meth_idx\', \'IDX_EEFE96319883967\')');
        $this->addSql('CALL rename_index(\'user_theories\', \'id_user\', \'IDX_101FF28A76ED395\')');
        $this->addSql('CALL rename_index(\'user_theories\', \'id_theory\', \'IDX_101FF286441A32F\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
