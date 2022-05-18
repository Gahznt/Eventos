require('dependent-dropdown');

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
    let $recordType = $('input[name$="[recordType]"]');

    $('input[name$="[identifier]"]').unmask();
    $('input[name$="[phone]"]').unmask();
    $('input[name$="[zipcode]"]').unmask();

    if ('0' == $recordType.val()) {
        $('input[name$="[identifier]"]').mask('000.000.000-00');
        $('input[name$="[phone]"]').mask(maskPhoneBehavior, maskPhoneOptions);
        $('input[name$="[zipcode]"]').mask('00000-000');
    }
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

function addNewFormAcademic() {
    let $collectionHolderAcademic = $('#academic-container-form');

    let prototype = $collectionHolderAcademic.data('prototype');
    let index = $collectionHolderAcademic.data('index');
    let newForm = prototype;
    if (!newForm) {
        return;
    }
    newForm = newForm.replace(/__name__/g, index);

    $collectionHolderAcademic.data('index', index + 1);

    let $panel = $('<div class="formacao mb-5"></div>').append(newForm);

    $panel.find('select').customSelect2();

    // if (index > 0) {
    addRemoveButtonAcademic($panel.find('.fieldsetBg'));
    // }

    $collectionHolderAcademic.append($panel);
}

function addRemoveButtonAcademic($panel) {
    let $removeButton = $($.parseHTML($('#academic-remove').html()));
    let $panelFooterTop = $('<div class="row btFormacao"></div>');
    let $panelFooter = $('<div class="col-lg-12 form-group"></div>').append($removeButton);
    $panelFooterTop.append($panelFooter);
    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.formacao').slideUp(1000, function () {
            $(this).remove();
        })
    });
    $panel.append($panelFooterTop);
}

function stepStart() {

    $('select').customSelect2();

    $('.invalid-feedback').show();

    if ($('#academic-container-form').find('.fieldsetBg').length === 0) {
        // addNewFormAcademic();
    } else {
        $('#academic-container-form .formacao').each(function (index, item) {
            const $panel = $(this);

            // if (index > 0) {
            addRemoveButtonAcademic($panel.find('.fieldsetBg'));
            // }
        });
    }

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
}

$(document).ready(function () {

    stepStart();

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

    $(document).on('click', '#btAcademicAdd', function (e) {
        e.preventDefault();
        addNewFormAcademic();
    });

    const $form = $('#formUserEdit');
    $form.find('.card-header').removeClass('invalid').removeClass('valid');
    $form.find('.card-header').addClass('valid');
    $form.find('.form-error').closest('.card').find('.card-header').removeClass('valid').addClass('invalid');
});
