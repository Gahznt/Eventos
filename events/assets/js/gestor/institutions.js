require('dependent-dropdown');

$(document).ready(function () {
    $(document).on('submit', 'form#institutionForm', function (e) {
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
                // alert('Dados gravados com sucesso.');
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
    })

    let institution_city = $("#institution_city");

    institution_city.depdrop({
        depends: ['institution_state'],
        url: institution_city.attr('route'),
        placeholder: institution_city.attr('select'),
        loading: false
    });
});
