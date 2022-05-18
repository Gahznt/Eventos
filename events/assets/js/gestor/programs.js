require('dependent-dropdown');
require('jquery-mask-plugin');

$(document).ready(function () {
    function maskPhoneBehavior(val) {
        return val.replace(/\D/g, '').length === 11
            ? '(00) 00000-0000'
            : '(00) 0000-00009';
    }

    const maskPhoneOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(maskPhoneBehavior.apply({}, arguments), options);
        }
    };

    $('input[name$="[phone]"]').mask(maskPhoneBehavior, maskPhoneOptions);
    $('input[name$="[cellphone]"]').mask(maskPhoneBehavior, maskPhoneOptions);

    $(document).on('submit', 'form#programForm', function (e) {
        e.preventDefault();

        let $form = $(this);

        let dataForm = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            beforeSend: () => {
                $form.find('.card-header').removeClass('invalid').removeClass('valid');
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $('#btnLoad').addClass('fa fa-spinner fa-spin');
            },
            success: function (data, statusCode, xhr) {

                window.location = $('#manager-back-route').prop('href');
            },
            error: function (data) {
                $form.addClass('was-validated');

                $form.html($(data.responseText).html());

                $form.find('.card-header').addClass('valid');
                $form.find('.form-error').closest('.card').find('.card-header').removeClass('valid').addClass('invalid');

                $('.invalid-feedback').show();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    });

    // let $state = $("#program_state");
    let $city = $("#program_city");

    /*$state.depdrop({
        // initDepends: ['program_country'],
        // initialize: true,
        depends: ['program_country'],
        url: $state.attr('route'),
        placeholder: $state.attr('select'),
        loading: false
    });*/

    $city.depdrop({
        // initDepends: ['program_state'],
        // initialize: true,
        depends: [/*'program_country', */'program_state'],
        url: $city.attr('route'),
        placeholder: $city.attr('select'),
        loading: false
    });
});
