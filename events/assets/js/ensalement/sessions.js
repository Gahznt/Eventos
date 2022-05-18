$(document).ready(function () {

    const addMask = () => {
        $('[name="system_ensalement_sessions[date]"]').mask('00/00/0000');
        $('[name="system_ensalement_sessions[start]"]').mask('00:00');
        $('[name="system_ensalement_sessions[end]"]').mask('00:00');
    };

    addMask();

    $(document).on('submit', 'form#sessionsForm', function (e) {
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
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $form.find('#btnLoad').addClass('fa fa-spinner fa-spin');
            },
            success: function (data, statusCode, xhr) {
                if ($('#back-route').length > 0) {
                    window.location = $('#back-route').prop('href');
                } else {
                    window.location.reload();
                }
            },
            error: function (data) {
                $form.addClass('was-validated');

                $form.html($(data.responseText).html());

                $('.invalid-feedback').show();

                addMask();
            },
            complete: function () {
                $form.find('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    })
});
