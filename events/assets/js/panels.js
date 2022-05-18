require('dependent-dropdown');
require('jquery-mask-plugin');

let $collectionHolderPanelist;

function addNewFormPanelist() {
    let prototype = $collectionHolderPanelist.data('prototype');
    let index = $collectionHolderPanelist.data('index');
    let newForm = prototype;
    if (!newForm) {
        return;
    }
    newForm = newForm?.replaceAll('__name__', index);
    newForm = newForm?.replaceAll('@panelistid', index + 1);
    index++;
    $collectionHolderPanelist.data('index', index);
    let $panel = $('<div class="painelista mb-3"></div>').append(newForm);

    if (index > 1) {
        addRemoveButtonPanelist($panel.find('.fieldsetBg'));
    }

    $collectionHolderPanelist.append($panel);

    checkPanelsCount();
}

function addRemoveButtonPanelist($panel) {

    let $removeButton = $($.parseHTML($('#panelist-remove').html()));
    let $panelFooterTop = $('<div class="row"></div>');
    let $panelFooter = $('<div class="col-lg-12 btPainelista"></div>').append($removeButton);
    $panelFooterTop.append($panelFooter);
    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.painelista').slideUp(1000, function () {
            $(this).remove();
            checkPanelsCount();
        })
    });
    $panel.append($panelFooterTop);
}

function fixNumberChars($field, limit, $message) {
    const initialValue = $field.val();
    const currentNumberChars = initialValue.length;
    $field.val(initialValue.substr(0, limit));
    $message.text(limit - currentNumberChars);
}

function checkPanelsCount() {
    const length = $('#panelist-container-form').find('.painelista').length;

    if (length >= 5) {
        $('#btPanelAdd').prop('disabled', true).addClass('disabled');
    } else {
        $('#btPanelAdd').prop('disabled', false).removeClass('disabled');
    }

    $collectionHolderPanelist.find('.fieldsetBg').each(function (index) {
        const $title = $(this).find('#title');
        $title.text($title.text().replace(/\d+/, index + 1));
    });
}

function stepStart() {
    $('.invalid-feedback').show();

    $collectionHolderPanelist = $('#panelist-container-form');
    $collectionHolderPanelist.data('index', $collectionHolderPanelist.find('.painelista').length);

    if ($collectionHolderPanelist.find('.fieldsetBg').length === 0) {
        addNewFormPanelist();
    } else {
        $collectionHolderPanelist.find('.painelista').each(function (index, item) {
            const $panel = $(this);
            if (index > 0) {
                addRemoveButtonPanelist($panel.find('.fieldsetBg'));
            }
        });
    }

    fixNumberChars($('#panel_justification'), 4000, $('#justification_remaining_chars_counter'));
    fixNumberChars($('#panel_suggestion'), 800, $('#suggestion_remaining_chars_counter'));

    checkPanelsCount();
}

$(document).ready(function () {

    stepStart();

    $(document).on('change', '.countryPanelist', function (e) {
        let count = $(this).data('count');

        if ($(this).val() !== "31" && $(this).val() !== "") {
            $(`#label-cpf-${count}`).html("Passport");
        } else {
            $(`#label-cpf-${count}`).html("CPF");
        }
    });

    $(document).on('change', '#countryProponent', function (e) {
        if ($(this).val() !== "31" && $(this).val() !== "") {
            $(`#cpf-proponente`).html("Passport");
        } else {
            $(`#cpf-proponente`).html("CPF");
        }
    });

    $(document).on('change', '.custom-file-input', function () {
        let filename = this.files[0].name;
        let count = $(this).data('count');
        $(`#custom-file-label_${count}`).html(filename);
    });

    $(document).on('click', '#btPanelAdd', function (e) {
        e.preventDefault();
        const length = $('#panelist-container-form').find('.painelista').length;
        if (length >= 5) {
            return;
        }

        addNewFormPanelist();
    });

    $(document).on('click', '.cpfSearchProponente', function (e) {
        e.preventDefault();
        let cpf = $('#cpf-proponente').val();
        let country = $('#countryProponent option:selected').val();

        $('#proponentIdFake').val('');
        $('#proponentId').val('');
        $.ajax({
            url: $(this).data('route'),
            type: 'GET',
            data: {countryId: country, cpf: cpf},
            success: function (response, statusCode, xhr) {

                $('#proponentIdFake').val(response.data.name);
                $('#proponentId').val(response.data.id);

            },
            error: function () {
                $('#proponentIdFake').val('Não encontrado');
                $('#proponentId').val('');
            }
        });
    });

    $(document).on('click', '.cpfSearch', function (e) {
        e.preventDefault();
        let count = $(this).data('count');
        let cpf = $(`input[name*='[${count}][cpf]'`).val();
        let country = $(`select[name*='[${count}][countryId]'] option:selected`).val();

        $(`input[name*='[${count}][userAuthorId]'`).val('');
        $(`input[name*='[${count}][userAuthorIdFake]'`).val('');
        $.ajax({
            url: $(this).data('route'),
            type: 'GET',
            data: {countryId: country, cpf: cpf},
            success: function (response, statusCode, xhr) {

                let fields = $('.painelista .fieldsetBg');
                let id = response.data.id;
                let name = response.data.name;
                $.each(fields, function () {
                    if (count > 1) {
                        let value = $(this).find(`input[name*='[${count - 1}][panelistId]']`).val();
                        if (parseInt(value) !== id) {
                            $(`input[name*='[${count}][panelistId]'`).val(id);
                            $(`input[name*='[${count}][panelistIdFake]'`).val(name);
                        } else {
                            $(`input[name*='[${count}][panelistIdFake]'`).val('Panilesita já adicionado');
                            $(`input[name*='[${count}][panelistId]'`).val("");
                        }
                    } else {
                        $(`input[name*='[${count}][panelistId]'`).val(id);
                        $(`input[name*='[${count}][panelistIdFake]'`).val(name);
                    }
                });

            },
            error: function () {
                $(`input[name*='[${count}][panelistIdFake]'`).val('Não encontrado');
                $(`input[name*='[${count}][panelistId]'`).val("");
            }
        });
    });

    $(document).on('submit', 'form#formPanelStep', function (e) {
        e.preventDefault();

        $('form#formPanelStep').removeClass('was-validated');

        $('.invalid-feedback').hide();

        let dataForm = new FormData(this);

        let size = 0;

        for (f = 0; f <= $('.painelista .custom-file').length; f++) {

            let list = $(`input[name='panel[panelsPanelists][${f}][proponentCurriculumPdfPath]']`);
            if (list.length > 0) {
                for (i = 0; i <= list.length; i++) {

                    if ($(list[i])[0] && $(list[i])[0].files[0]) {
                        size = size + $(list[i])[0].files[0].size;
                    }
                }
            }
        }

        let list = $('#panel_proponentCurriculumPdfPath');

        if (list[0].files.length > 0) {
            size = size + list[0].files[0].size;
        }

        if ((size / (1024 * 1024)).toFixed(2) > 2.00) {
            $('.invalid-feedback').append('Files over size');
            $('.invalid-feedback').show();
            $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            return false;
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            success: function (data, statusCode, xhr) {
                let handler = $("#panelForm");
                let step = xhr.getResponseHeader('x-step');
                let last_step = step;
                --last_step;

                $('#btnLoad').removeClass('fa fa-spinner fa-spin');

                if (data.saved === true && data.pass === true) {

                    if (step) {
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        $(`#panelTimeLine .step`).removeClass('active').addClass('complete');
                        handler.find('.stepsTab').hide();
                        handler.find(".stepsTab[data-step=4]").show();
                        $('#step').val(4);
                        $('.btCadastroVoltar').hide();
                        $('.btCadastro').hide();
                        $('#btnLoad').removeClass('fa fa-spinner fa-spin');
                    }

                }

                if (data.saved === false && data.pass === true) {

                    if (step) {

                        handler.find(".stepsTab").hide();
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        handler.find(`.stepsTab[data-step=${step}]`).show();
                        handler.find('#step').val(step);

                        let handlerStep = $('#step');

                        $('.stepsTab').hide();
                        $(`#panelTimeLine .step[data-step="${last_step}"]`).removeClass('active').addClass('complete');
                        $(`#panelTimeLine .step[data-step="${step}"]`).addClass('active');

                        handlerStep.val(step);
                        $('.btCadastroVoltar').removeClass('disabled').show();
                        $(`.stepsTab[data-step=${step}]`).show();

                    }
                }

                $('.mobileSteps span').text(step);
            },
            error: function (data) {
                $('form#formPanelStep').addClass('was-validated');
                let step = data.getResponseHeader('x-step');
                $('#step').val(step);
                $("#panelForm").html(data.responseText).find(`.stepsTab[data-step=${step}]`).show();
                //$('.form-error').parent().find('input').addClass('error-input');
                $('.invalid-feedback').show();
                stepStart();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    });

    // Begin Controll navegation form
    let step = parseInt($('#step').val());

    $('.btCadastroVoltar').hide();

    $(`.stepsTab[data-step=${step}]`).show();

    $(document).on('click', '.btCadastro', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 3) {
            $(this).addClass('disabled');
        }

        $('#btnLoad').addClass('fa fa-spinner fa-spin');
        $('form#formPanelStep').submit();
    });

    $(document).on('click', '.btCadastroVoltar', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 1) {
            $(this).removeClass('disabled').show();
            $('.stepsTab').hide();
            $(`#panelTimeLine .step[data-step="${step}"]`).removeClass('active').removeClass('complete');
            --step;
            $(`#panelTimeLine .step[data-step="${step}"]`).addClass('active').removeClass('complete');
            $(`.stepsTab[data-step=${step}]`).show();
            handlerStep.val(step);
        }

        if (step === 1 || step > 3) {
            $(this).addClass('disabled').hide();
        }

        $('.mobileSteps span').text(step);
    });

    $(document).on('keyup blur', '#panel_justification', function (e) {
        const $self = $(this);
        fixNumberChars($self, 4000, $('#justification_remaining_chars_counter'));
    });

    $(document).on('keyup blur', '#panel_suggestion', function (e) {
        const $self = $(this);
        fixNumberChars($self, 800, $('#suggestion_remaining_chars_counter'));
    });
});
