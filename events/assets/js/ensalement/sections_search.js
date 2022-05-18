$(document).ready(function () {
    let $form = $('form#sectionScheduleSearchForm');

    // $form.find('select').customSelect2();

    $(document).on('change', '#search_division', function (e) {
        let $self = $(this);
        $('#search_theme').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#search_division', function (e) {

        let dataForm = new FormData($form[0]);

        $.ajax({
            url: $('form#sectionScheduleSearchFilterForm').attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            beforeSend: () => {
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $form.find('.activity-indicator').removeClass('d-none');
            },
            success: function (data, statusCode, xhr) {
                $form.html($(data).html());
            },
            error: function (data) {
                $form.addClass('was-validated');

                $form.html($(data.responseText).html());

                $('.invalid-feedback').show();
            },
            complete: function () {
                $form.find('.activity-indicator').addClass('d-none');
                $form.find('select').customSelect2();
            }
        });
    });

});
