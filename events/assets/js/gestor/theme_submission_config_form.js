$(document).ready(function () {
    $(document).on('change', '#manager_theme_submission_config_isAvailable,' +
        '#manager_theme_submission_config_isCurrent,' +
        '#manager_theme_submission_config_isEvaluationAvailable,' +
        '#manager_theme_submission_config_isResultAvailable,' +
        '#theme_submission_config_isAvailable,' +
        '#theme_submission_config_isCurrent,' +
        '#theme_submission_config_isEvaluationAvailable,' +
        '#theme_submission_config_isResultAvailable', function (e) {

        var $self = $(this);

        if (!$self.is(':checked')) {
            return;
        }

        if (confirm("Ao ativar esta opção todas as outras serão desativadas. \nDeseja continuar?")) {
            return;
        }

        e.preventDefault();
        e.stopImmediatePropagation();

        $self.prop('checked', false);
    });
});
