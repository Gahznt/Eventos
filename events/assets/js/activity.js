require('dependent-dropdown');
require('jquery-mask-plugin');

let $collectionHolderPanelist;
let $collectionHolderGuest;

function addNewFormUser(handler) {
    let prototype = handler.data('prototype');
    // isso é setado no $.ready
    let index = handler.data('index');

    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    index = index + 1;
    handler.data('index', index);
    let $activity = $(newForm);

    handler.find('.collection-items').append($activity);
    collectionActions(handler);
}

function collectionActions(handler) {
    handler.find('.collection-item').each(function (index, e) {
        $(this).find('.collection-item-position').text(index + 1);
    });

    handler.find('.btn-remove-collection-item:first').addClass('disabled').hide();
    handler.find('.btn-remove-collection-item:not(:first)').removeClass('disabled').show();
}

function handleStep(step) {
    step = parseInt(step);

    if (step < 1) {
        return;
    }

    $('#step').val(step);
    $('.stepsTab').hide();
    $(`.stepsTab[data-step=${step}]`).show();

    $(`#activityTimeLine .step`).removeClass('active').removeClass('complete');

    for (let i = 1; i <= step; i++) {
        if (i <= step) {
            $(`#activityTimeLine .step[data-step="${i}"]`).addClass('complete');
        } else {
            $(`#activityTimeLine .step[data-step="${i}"]`).addClass('active');
        }
    }

    if (step === 1) {
        $('.btCadastroVoltar').addClass('disabled').hide();
    } else {
        $('.btCadastroVoltar').removeClass('disabled').show();
    }

    if (step === $('.stepsTab').length) {
        $('.btCadastroVoltar').addClass('disabled').hide();
        $('.btCadastro').addClass('disabled').hide();
    } else {
        $('.btCadastro').removeClass('disabled').show();
    }

    if ($collectionHolderPanelist.find('.collection-item').length === 0) {
        addNewFormUser($collectionHolderPanelist);
    }

    if ($collectionHolderGuest.find('.collection-item').length === 0) {
        addNewFormUser($collectionHolderGuest);
    }

    $('.mobileSteps span').text(step);

    tinymce.remove();
    tinymce.init({
        selector: 'textarea.htmleditor',
        language: 'pt_BR',
        height: 300,
        menubar: false,
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table directionality',
            'emoticons template paste textpattern imagetools'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
        image_advtab: true,
        init_instance_callback: function (editor) {
            editor.on('input', function (e) {
                $(`#${e.target.dataset.id}`).val(tinymce.get(e.target.dataset.id).getContent()).change();
            });
            editor.on('change', function (e) {
                $(`#${e.target.id}`).val(tinymce.get(e.target.id).getContent()).change();
            });
        }
    });
}

$(document).ready(function () {

    $(document).on('change', '.countryGuests', function (e) {
        let count = $(this).data('count');

        if ($(this).val() !== "31" && $(this).val() !== "") {
            $(`#label-cpf-${count}`).html("Passport");
        } else {
            $(`#label-cpf-${count}`).html("CPF");
        }
    });

    $(document).on('change', '.countryPainelist', function (e) {
        let count = $(this).data('count');

        if ($(this).val() !== "31" && $(this).val() !== "") {
            $(`#label-cpf-${count}`).html("Passport");
        } else {
            $(`#label-cpf-${count}`).html("CPF");
        }
    });

    // TODO melhorar
    $(document).on('change', '.painelist', function () {
        let filename = this.files[0].name;
        let count = $(this).data('count');
        $(`#custom-file-label_${count}`).html(filename);
    });

    $(document).on('change', '.proponent', function () {
        let filename = this.files[0].name;
        let count = $(this).data('count');
        $(`#proponent-custom-file-label_${count}`).html(filename);
    });

    $(document).on('click', '.btn-add-collection-item', function (e) {
        e.preventDefault();
        addNewFormUser($(this).closest('.collection-container'));
    });

    $(document).on('click', '.btn-remove-collection-item', function (e) {
        e.preventDefault();
        let $self = $(this);
        if ($self.hasClass('disabled')) {
            return;
        }

        let $collectionItem = $self.closest('.collection-item');
        let $container = $collectionItem.closest('.collection-container');

        $collectionItem.slideUp(1000, function () {
            $collectionItem.remove();
            collectionActions($container);
        });
    });

    $(document).on('keypress', 'input[name$="[name]"]', function (e) {
        let $self = $(this);

        let $collectionItem = $self.closest('.collection-item');

        $collectionItem.find('input[name$="[cpf]"]').val('');
        $collectionItem.find('select[name$="[country]"]').val('');
        $collectionItem.find('.input-user').val('');
        $collectionItem.find('.user-fake-error').html('');
    });

    $(document).on('click', '.cpfSearch', function (e) {
        e.preventDefault();

        let $self = $(this);

        let $collectionItem = $self.closest('.collection-item');
        let $container = $collectionItem.closest('.collection-container');

        let cpf = $collectionItem.find('input[name$="[cpf]"]').val();
        let country = $collectionItem.find('select[name$="[country]"] option:selected').val();

        $collectionItem.find('.input-user').val('');
        $collectionItem.find('.input-user-fake').val('');
        $collectionItem.find('.user-fake-error').html('');

        $.ajax({
            url: $(this).data('route'),
            type: 'GET',
            data: {countryId: country, cpf: cpf},
            success: function (response, statusCode, xhr) {
                let id = response.data.id;
                let name = response.data.name;

                let valueExists = false;
                $container.find(`.input-user`).each(function () {
                    valueExists = valueExists || id == $(this).val();
                });

                if (valueExists) {
                    $collectionItem.find('.input-user').val('');
                    $collectionItem.find('.input-user-fake').val('');
                    $collectionItem.find('.user-fake-error').html('<div class="badge badge-danger">Já adicionado</div>');
                } else {
                    $collectionItem.find('.input-user').val(id);
                    $collectionItem.find('.input-user-fake').val(name);
                    $collectionItem.find('.user-fake-error').html('');
                }
            },
            error: function () {
                $collectionItem.find('.input-user').val('');
                $collectionItem.find('.input-user-fake').val('');
                $collectionItem.find('.user-fake-error').html('<div class="badge badge-danger">Não encontrado</div>');
            }
        });
    });

    $(document).on('submit', 'form#formActivityStep', function (e) {
        e.preventDefault();

        let $form = $(this);

        let dataForm = new FormData(this);
        let size = 0;

        for (f = 0; f <= $('.painelista .custom-file').length; f++) {

            let list = $(`input[name='activity[panelists][${f}][proponentCurriculumPdfPath]']`);
            for (i = 0; i <= list.length; i++) {

                if ($(list[i])[0] && $(list[i])[0].files[0]) {
                    size = size + $(list[i])[0].files[0].size;
                }
            }
        }

        for (f = 0; f <= $('.convidado .custom-file').length; f++) {

            let list = $(`input[name='activity[guests][${f}][proponentCurriculumPdfPath]']`);
            for (i = 0; i <= list.length; i++) {

                if ($(list[i])[0] && $(list[i])[0].files[0]) {
                    size = size + $(list[i])[0].files[0].size;
                }
            }
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
            beforeSend: () => {
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $('#btnLoad').addClass('fa fa-spinner fa-spin');
            },
            success: function (data, statusCode, xhr) {
                let step = xhr.getResponseHeader('x-step');
                handleStep(step);
            },
            error: function (data) {
                $form.addClass('was-validated');

                $("#activityForm").html(data.responseText);

                let step = data.getResponseHeader('x-step');
                handleStep(step);
                collectionActions($('#panelist-container-form'));
                collectionActions($('#guest-container-form'));
                $('.invalid-feedback').show();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    });

    $(document).on('click', '.btCadastro', function () {
        $('form#formActivityStep').submit();
    });

    $(document).on('click', '.btCadastroVoltar', function () {
        let step = $('#step').val();
        $('.invalid-feedback').html('');
        $('.invalid-feedback').hide();
        handleStep(step - 1);
    });

    // inicia a collection de painelistas
    $collectionHolderPanelist = $('#panelist-container-form');
    $collectionHolderPanelist.data('index', $collectionHolderPanelist.find('.collection-item').length);

    // inicia a collection de convidados
    $collectionHolderGuest = $('#guest-container-form');
    $collectionHolderGuest.data('index', $collectionHolderGuest.find('.collection-item').length);

    handleStep(1);
});
