$(document).ready(function () {

    function onChangePaymentMode() {
        let hasFreeIndividualAssociation = 0;

        const $el = $('input[name="edition_signup[paymentMode]"]:checked');

        if ($el.length) {
            hasFreeIndividualAssociation = $el.data('has-free-individual-association');
        }

        $('#containerWantFreeIndividualAssociation').toggleClass('d-none', !hasFreeIndividualAssociation);
    }

    $(document).on('change', 'input[name="edition_signup[paymentMode]"]', function (e) {
        onChangePaymentMode();
    });

    onChangePaymentMode();

    $(document).on('change', '#edition_signup_wantFreeIndividualAssociation', function (e) {
        const $el = $(this);

        if (!$el.prop('checked')) {
            $('#edition_signup_freeIndividualAssociationDivision').val('').trigger('change');
        }
        $('#containerFreeIndividualAssociationDivision').toggleClass('d-none', !$el.prop('checked'));
    });

    $('#edition_signup_wantFreeIndividualAssociation').trigger('change');

    $(document).on('change', '#edition_signup_file', function () {
        if (!this.files || !this.files[0] || !this.files[0].size) {
            return;
        }

        $('#filename-label').text(this.files[0].name);
        $('#btn-remove-file').removeClass('d-none');
    });

    $(document).on('click', '#btn-remove-file', function () {
        let $file = $('#edition_file_file');
        let self = $(this);

        $('#filename-label').text('Nenhum arquivo selecionado');
        self.addClass('d-none');
        $file.val(null);
    });

    $('.btCadastroVoltar').hide();

    $(document).on('submit', '#eventLoginForm #formLoginEvent', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('defaults', 'false');
            },
            success: function (data, statusCode, xhr) {
                let error = xhr.getResponseHeader('x-error');
                if (error) {
                    $('#eventLoginForm #btnLoginLoad').addClass('d-none');
                    $('#eventLoginForm').html(data);
                    $('#eventLoginForm #formLoginEvent').addClass('was-validated');
                    $('#eventLoginForm #inputPassword').addClass('is-invalid');
                    $('#eventLoginForm #BtFormLogin').addClass('follow');
                } else {
                    location.reload();
                }
            },
        });
    });

    $(document).on('click', '#eventLoginForm #BtFormLogin', function (e) {
        e.preventDefault();
        $('#eventLoginForm #BtFormLogin').removeClass('follow');
        $('#eventLoginForm #btnLoginLoad').removeClass('d-none');
        $('#eventLoginForm #formLoginEvent').submit();
    });

    $(document).on('click', '.btCadastro', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 4) {
            $(this).addClass('disabled');
        }

        $('#btnLoad').addClass('fa fa-spinner fa-spin');
        $('form#formEventSignUp').submit();
    });

    $(document).on('click', '.btCadastroVoltar', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 1) {
            $(this).removeClass('disabled').show();
            $('.stepsTab').hide();
            $(`#eventSignUpTimeLine .step[data-step="${step}"]`).removeClass('active').removeClass('complete');
            --step;
            $(`#eventSignUpTimeLine .step[data-step="${step}"]`).addClass('active').removeClass('complete');
            $(`.stepsTab[data-step=${step}]`).show();
            handlerStep.val(step);
        }

        if (step === 1 || step > 4) {
            $(this).addClass('disabled').hide();
        }

    });

    $(document).on('submit', 'form#formEventSignUp', function (e) {
        e.preventDefault();
        $('.invalid-feedback').html('');
        $('form#formEventSignUp').removeClass('was-validated');

        $('.invalid-feedback').hide();

        let dataForm = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            success: function (data, statusCode, xhr) {
                let step = xhr.getResponseHeader('x-step');
                $('#step').val(step);
                let handler = $("#eventSignUpForm");
                handler.html(data.content).find(`.stepsTab[data-step=${step}]`).show();

                let last_step = step;
                --last_step;

                $('#btnLoad').removeClass('fa fa-spinner fa-spin');

                if (data.saved === true && data.pass === true) {

                    if (step) {
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        $(`#eventSignUpTimeLine .step`).removeClass('active').addClass('complete');
                        handler.find('.stepsTab').hide();
                        handler.find(".stepsTab[data-step=3]").show();
                        $('#step').val(3);
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
                        $(`#eventSignUpTimeLine .step[data-step="${last_step}"]`).removeClass('active').addClass('complete');
                        $(`#eventSignUpTimeLine .step[data-step="${step}"]`).addClass('active');

                        handlerStep.val(step);
                        $('.btCadastroVoltar').removeClass('disabled').show();
                        $(`.stepsTab[data-step=${step}]`).show();

                    }
                }

                $('.mobileSteps span').text(step);
            },
            error: function (data) {
                $('form#eventSignUpTimeLine').addClass('was-validated');
                let step = data.getResponseHeader('x-step');
                $('#step').val(step);
                $("#eventSignUpForm").html(data.responseText).find(`.stepsTab[data-step=${step}]`).show();
                $('.invalid-feedback').show();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');

                onChangePaymentMode();
                $('#edition_signup_wantFreeIndividualAssociation').trigger('change');

                $('select').customSelect2();
            }
        });
    });

    let step = parseInt($('#step').val());
    let is_guest = $('#is_guest').val();

    if (is_guest === "false") {
        $('.btCadastro').show();
        $(`#eventSignUpTimeLine .step[data-step="1"]`).removeClass('active').addClass('complete');
        $(`#eventSignUpTimeLine .step[data-step="1"]`).addClass('active');
        $(`#eventSignUpTimeLine .step[data-step="2"]`).removeClass('active').addClass('complete');
    }

    if (is_guest === "true") {
        $('.btCadastro').hide();
    }

    $(`.stepsTab[data-step=${step}]`).show();
});
