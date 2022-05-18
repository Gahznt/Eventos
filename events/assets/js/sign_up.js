require('dependent-dropdown');

const i18n = {
    'pt_br': {
        'Passport': 'Passaporte',
        'CPF': 'CPF',
        'Identifier': 'Identificador'
    },
    'en': {
        'Passport': 'Passport',
        'CPF': 'CPF',
        'Identifier': 'Identifier',
    },
    'es': {
        'Passport': 'Passaporte',
        'CPF': 'CPF',
        'Identifier': 'Identificador',
    }
};

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

function setState() {
    const $localeChecked = $('input[name$="[locale]"]:checked');
    const locale = $localeChecked.val();
    const $recordType = $('input[name$="[recordType]"]');
    let $recordTypeChecked = $('input[name$="[recordType]"]:checked');
    const $recordTypeFake = $('input[name="recordTypeFake"]');
    const $isForeignUseCpf = $('input[name$="[isForeignUseCpf]"]');
    let $isForeignUseCpfChecked = $('input[name$="[isForeignUseCpf]"]:checked');
    const $isForeignUsePassport = $('input[name$="[isForeignUsePassport]"]');
    let $isForeignUsePassportChecked = $('input[name$="[isForeignUsePassport]"]:checked');
    const $identifier = $('input[name$="[identifier]"]');
    const $identifierFake = $('input[name="identifierFake"]');
    const $country = $('select[name$="[country]"]');
    const $email = $('input[name$="[email]"]');
    const $emailFake = $('input[name="emailFake"]');

    $isForeignUseCpf.closest('.form-group').hide();
    $isForeignUsePassport.closest('.form-group').hide();
    $identifier.closest('.form-group').hide();
    $identifierFake.closest('.form-group').hide();

    $identifier.closest('.form-group').find('label').html('');
    $identifierFake.closest('.form-group').find('label').html('');

    $('input[name$="[identifier]"]').unmask();
    $('input[name$="[phone]"]').unmask();
    $('input[name$="[zipcode]"]').unmask();

    if (0 === $recordTypeChecked.length) {
        $recordType.eq(0).prop('checked', true);
        $recordTypeChecked = $('input[name$="[recordType]"]:checked');
    }

    if ('0' == $recordTypeChecked.val()) {
        $identifier.closest('.form-group').show();
        $identifier.closest('.form-group').find('label').html(i18n[locale]['CPF']);

        $identifierFake.closest('.form-group').show();
        $identifierFake.closest('.form-group').find('label').html(i18n[locale]['CPF']);

        $('input[name$="[identifier]"]').mask('000.000.000-00');
        $('input[name$="[phone]"]').mask(maskPhoneBehavior, maskPhoneOptions);
        $('input[name$="[zipcode]"]').mask('00000-000');
    } else {
        $isForeignUseCpf.closest('.form-group').show();

        if (0 === $isForeignUseCpfChecked.length) {
            $isForeignUseCpf.eq(0).prop('checked', true);
            $isForeignUseCpfChecked = $('input[name$="[isForeignUseCpf]"]:checked');
        }

        if ('1' == $isForeignUseCpfChecked.val()) {
            $identifier.closest('.form-group').show();
            $identifier.closest('.form-group').find('label').html(i18n[locale]['CPF']);

            $identifierFake.closest('.form-group').show();
            $identifierFake.closest('.form-group').find('label').html(i18n[locale]['CPF']);
        } else {
            $isForeignUsePassport.closest('.form-group').show();

            if (0 === $isForeignUsePassportChecked.length) {
                $isForeignUsePassport.eq(0).prop('checked', true);
                $isForeignUsePassportChecked = $('input[name$="[isForeignUsePassport]"]:checked');
            }

            if ('1' == $isForeignUsePassportChecked.val()) {
                $identifier.closest('.form-group').show();
                $identifier.closest('.form-group').find('label').html(i18n[locale]['Passport']);

                $identifierFake.closest('.form-group').show();
                $identifierFake.closest('.form-group').find('label').html(i18n[locale]['Passport']);
            } else {
                // $identifier.closest('.form-group').show();
                // $identifier.closest('.form-group').find('label').html(i18n[locale]['Identifier']);
                //
                // $identifierFake.closest('.form-group').show();
                // $identifierFake.closest('.form-group').find('label').html(i18n[locale]['Identifier']);
            }
        }
    }

    $recordTypeFake.val($recordTypeChecked.data('label'));
    $identifierFake.val($identifier.val());
    $emailFake.val($email.val());
}

setState();

$(document).on('change', '' +
    //'input[name$="[locale]"], ' +
    'input[name$="[recordType]"], ' +
    'input[name$="[isForeignUseCpf]"], ' +
    'input[name$="[isForeignUsePassport]"]', function (e) {
    setState();
});

$(document).on('change', 'input[name$="[locale]"]', function (e) {
    const $self = $(this);
    const $btn = $(`.btnLanguage[data-language="${$self.val()}"]`);
    $btn.length && $btn.trigger('click');
});

$(document).on('click', '#clear-institution_programs', function (e) {
    $('select[name$="[institutionsPrograms][institutionFirstId]"]').val('').trigger('change');
    $('select[name$="[institutionsPrograms][programFirstId]"]').val('').trigger('change');
    $('select[name$="[institutionsPrograms][stateFirstId]"]').val('').trigger('change');
    $('input[name$="[institutionsPrograms][otherInstitutionFirst]"]').val('');
    $('input[name$="[institutionsPrograms][otherProgramFirst]"]').val('');

    $('select[name$="[institutionsPrograms][institutionSecondId]"]').val('').trigger('change');
    $('select[name$="[institutionsPrograms][programSecondId]"]').val('').trigger('change');
    $('select[name$="[institutionsPrograms][stateSecondId]"]').val('').trigger('change');
    $('input[name$="[institutionsPrograms][otherInstitutionSecond]"]').val('');
    $('input[name$="[institutionsPrograms][otherProgramSecond]"]').val('');
});

$.fn.selectBoxPopulate = function (data) {
    return this.each(function () {
        var $container = $(this);

        while ($container.find('option').length > 1) {
            $container.find('option').eq($container.find('option').length - 1).remove();
        }

        if (Object.keys(data).length > 0) {
            for (i in data) {
                $container.append('<option value="' + i + '">' + data[i] + '</option>');
            }
        }
    });
};

// Begin Controll navegation form
let step = parseInt($('#step').val());

$('.btCadastroVoltar').hide();

$(`.stepsTab[data-step=${step}]`).show();
$('#signup-main').show();

function stepStart() {

    $('select').customSelect2();

    $('.invalid-feedback').show();

    $('.date').mask('00/00/0000');

    let sign_up_state = $("#sign_up_step_1_state");
    let sign_up_city = $("#sign_up_step_1_city");
    let sign_up_theme_first = $("#theme_first_id");
    let sign_up_theme_second = $("#theme_second_id");
    let sign_up_keyword_first = $("#keyword_one");
    let sign_up_keyword_second = $("#keyword_two");
    let sign_up_keyword_three = $("#keyword_three");
    let sign_up_keyword_four = $("#keyword_four");

    sign_up_theme_first.depdrop({
        // initDepends: ['division_fist_id'],
        // initialize: true,
        depends: ['division_fist_id'],
        url: sign_up_theme_first.attr('route'),
        placeholder: sign_up_theme_first.attr('select'),
        loading: false
    });

    sign_up_theme_second.depdrop({
        // initDepends: ['division_second_id'],
        // initialize: true,
        depends: ['division_second_id'],
        url: sign_up_theme_second.attr('route'),
        placeholder: sign_up_theme_second.attr('select'),
        loading: false
    });

    sign_up_keyword_first.depdrop({
        // initDepends: ['division_fist_id', 'theme_first_id'],
        // initialize: true,
        depends: ['division_fist_id', 'theme_first_id'],
        url: sign_up_keyword_first.attr('route'),
        placeholder: sign_up_keyword_first.attr('select'),
        loading: false
    });

    sign_up_keyword_second.depdrop({
        // initDepends: ['division_fist_id', 'theme_first_id'],
        // initialize: true,
        depends: ['division_fist_id', 'theme_first_id'],
        url: sign_up_keyword_second.attr('route'),
        placeholder: sign_up_keyword_second.attr('select'),
        loading: false
    });

    sign_up_keyword_three.depdrop({
        // initDepends: ['division_second_id', 'theme_second_id'],
        // initialize: true,
        depends: ['division_second_id', 'theme_second_id'],
        url: sign_up_keyword_three.attr('route'),
        placeholder: sign_up_keyword_three.attr('select'),
        loading: false
    });

    sign_up_keyword_four.depdrop({
        // initDepends: ['division_second_id', 'theme_second_id'],
        // initialize: true,
        depends: ['division_second_id', 'theme_second_id'],
        url: sign_up_keyword_four.attr('route'),
        placeholder: sign_up_keyword_four.attr('select'),
        loading: false
    });

    sign_up_state.depdrop({
        // initDepends: ['sign_up_step_1_country'],
        // initialize: true,
        depends: ['sign_up_step_1_country'],
        url: sign_up_state.attr('route'),
        placeholder: sign_up_state.attr('select'),
        loading: false
    });

    sign_up_city.depdrop({
        // initDepends: ['sign_up_step_1_state'],
        // initialize: true,
        depends: ['sign_up_step_1_country', 'sign_up_step_1_state'],
        url: sign_up_city.attr('route'),
        placeholder: sign_up_city.attr('select'),
        loading: false
    });

    $('#btnLoad').removeClass('fa fa-spinner fa-spin');
    $('#btnLoadConfirm').removeClass('fa fa-spinner fa-spin');
    // End depdrops country, state and city
}

$(document).ready(function () {

    stepStart();

    $('#sign_up_userEvaluationArticles_wantEvaluate').checked = true;

    $(document).on('change', '[name$="[institutionFirstId]"], [name$="[institutionSecondId]"]', function (e) {
        const $self = $(this);

        const $panel = $self.closest('.jquery_institution_programs_row');

        const $program = $panel.find('[name$="[programFirstId]"], [name$="[programSecondId]"]');

        //$self.select2();
        const route = $self.data('route');
        const selected = parseInt($self.val());

        // const $program = $panel.find('[name$="[programFirstId]"], [name$="[programSecondId]"]');

        const $programsLoad = $panel.find('.programs-load');

        $panel.find('.other_institution_wrapper').toggleClass('d-none', selected !== 99999);
        $program.selectBoxPopulate([]);

        if (!selected) {
            return;
        }

        $.ajax({
            url: route,
            type: 'GET',
            data: {
                institution: selected
            },
            beforeSend: function () {
                $programsLoad.removeClass('d-none');
            },
            success: function (response, statusCode, xhr) {
                $program.selectBoxPopulate(response);
            },
            complete: function () {
                if (selected === 99999) {
                    $program.val(99999);
                }

                //$program.select2();
                $program.trigger('change');
                $programsLoad.addClass('d-none');
            }
        });
    });

    $(document).on('change', '[name$="[programFirstId]"], [name$="[programSecondId]"]', function (e) {
        const $self = $(this);

        const $panel = $self.closest('.jquery_institution_programs_row');

        $panel.find('.other_program_wrapper').toggleClass('d-none', parseInt($self.val()) !== 99999);
    });

    $(document).on('click', '.collection .btn-add', function (e) {
        e.preventDefault();

        var $collection = $(this).closest('.collection');

        var count = $collection.data('count');
        var template = $collection.data('prototype');

        var newFieldset = template.replace(/__name__/g, count);

        $collection.find('.fieldsets').append(newFieldset);

        $collection.data('count', count + 1);
    });

    $(document).on('click', '.collection .btn-remove', function (e) {
        e.preventDefault();

        var $fieldset = $(this).closest('.fieldset');

        $fieldset.slideUp(1000, function () {
            $fieldset.remove();
        });
    });

    $(document).on('submit', 'form#formSignStep', function (e) {
        e.preventDefault();

        $(this).removeClass('was-validated');
        $('.form-control').removeClass('error-input');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (data, statusCode, xhr) {
                if (data.responseText) {
                    $("#userCadastro").html(data.responseText);
                }

                let handler = $("#userCadastro");
                let step = xhr.getResponseHeader('x-step');
                let last_step = step;
                --last_step;

                $('.invalid-feedback').hide();

                if (data.saved === true && data.pass === true) {

                    if (step) {
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        $(`#signTimeLine .step`).removeClass('active').addClass('complete');
                        handler.find('.stepsTab').hide();
                        handler.find(".stepsTab[data-step=5]").show();
                        $('#step').val(5);
                        $('.btCadastroVoltar').hide();
                        $('.btCadastro').hide();
                        $('#btnLoad').removeClass('fa fa-spinner fa-spin');
                        $('#btnLoadConfirm').removeClass('fa fa-spinner fa-spin');
                        $("#formSignSuccessOff").hide();
                        $("#formSignSuccess").show();
                    }

                }

                if (data.saved === false && data.pass === true) {

                    if (step <= 4) {

                        handler.find(".stepsTab").hide();
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        handler.find(`.stepsTab[data-step=${step}]`).show();
                        handler.find('#step').val(step);

                        let handlerStep = $('#step');

                        $('.stepsTab').hide();
                        $(`#signTimeLine .step[data-step="${last_step}"]`).removeClass('active').addClass('complete');
                        $(`#signTimeLine .step[data-step="${step}"]`).addClass('active');

                        handlerStep.val(step);
                        if (step > 1) {
                            $('.btCadastroVoltar').removeClass('disabled').show();
                        }
                        $(`.stepsTab[data-step=${step}]`).show();

                        stepStart();
                    }

                    if (step >= 5) {
                        $('.btCadastroVoltar').hide();
                        $('.btCadastro').hide();
                        handler.find(".stepsTab").hide();
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        handler.find(`.stepsTab[data-step=${step}]`).show();
                        handler.find('#step').val(step);

                        $(`#signTimeLine .step[data-step="${last_step}"]`).removeClass('active').addClass('complete');
                        $(`#signTimeLine .step[data-step="${step}"]`).addClass('active');

                        $('#step').val(5);

                        $('#btnLoad').removeClass('fa fa-spinner fa-spin');
                        $('#btnLoadConfirm').removeClass('fa fa-spinner fa-spin');
                        $('#btnLoadConfirm').parent().show();
                        $("#formSignSuccessOff").show();
                        $("#formSignSuccess").hide();
                    }
                }

                $('.mobileSteps span').text(step);
            },
            error: function (data) {
                $("#userCadastro").html(data.responseText);
                $('form#formSignStep').addClass('was-validated');
                let step = data.getResponseHeader('x-step');
                $("#userCadastro").find(`.stepsTab[data-step=${step}]`).show();
                $('#step').val(step);
                $('.form-error').parent().find('input').addClass('error-input');
                stepStart();
            },
            complete: function () {
                setState();
            }
        });
    });

    $(document).on('click', '.btCadastro', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 4) {
            $(this).addClass('disabled');
        }

        $('#btnLoad').addClass('fa fa-spinner fa-spin');
        $('#btnLoadConfirm').addClass('fa fa-spinner fa-spin');
        $('form#formSignStep').submit();
    });

    $(document).on('click', '.btCadastroVoltar', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        // $('.invalid-feedback').hide();

        if (step > 1) {
            $(this).removeClass('disabled').show();
            $('.stepsTab').hide();
            $(`#signTimeLine .step[data-step="${step}"]`).removeClass('active').removeClass('complete');
            --step;
            $(`#signTimeLine .step[data-step="${step}"]`).addClass('active').removeClass('complete');
            $(`.stepsTab[data-step=${step}]`).show();
            handlerStep.val(step);
        }

        if (step <= 1 || step >= 5) {
            $(this).addClass('disabled').hide();
        }

        $('.mobileSteps span').text(step);
    });

});
