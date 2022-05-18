require('dependent-dropdown');

$(function () {
    const $checks = $('input[name="indications[]"]');
    $(document).on('change', 'input[name="indications[]"]', function () {
        if ($checks.filter(':checked').length >= 2) {
            $checks.filter(':not(:checked)').prop('disabled', true);
        } else {
            $checks.prop('disabled', false);
        }
    });

    $checks.trigger('change');

    $('select[name="system_evaluation_indications_search[theme]"]').depdrop({
        //initDepends: ['system_evaluation_indications_search_division'],
        //initialize: true,
        depends: ['system_evaluation_indications_search_division'],
        url: $('#system_evaluation_indications_search_theme').attr('route'),
        placeholder: $('#system_evaluation_indications_search_theme').attr('select'),
        loading: false
    });
});
