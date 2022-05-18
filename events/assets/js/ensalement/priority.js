$(document).ready(function () {

    let $form = $('form#priorityScheduleForm');

    $form.find('select').customSelect2();

    $(document).on('change', '#ensalement_priority_division', function (e) {
        let $self = $(this);
        $('#ensalement_priority_activityType').prop('disabled', true).val('').customSelect2();
        $('#ensalement_priority_activity').prop('disabled', true).val('').customSelect2();
        $('#ensalement_priority_panel').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_priority_activityType', function (e) {
        let $self = $(this);
        $('#ensalement_priority_activity').prop('disabled', true).val('').customSelect2();
        $('#ensalement_priority_panel').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_priority_date', function (e) {
        let $self = $(this);
        $('#ensalement_priority_systemEnsalementSessions').prop('disabled', true).val('').customSelect2();
        $('#ensalement_priority_systemEnsalementSlots').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_priority_systemEnsalementSessions', function (e) {
        let $self = $(this);
        $('#ensalement_priority_systemEnsalementSlots').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_priority_division, ' +
        '#ensalement_priority_activityType, ' +
        '#ensalement_priority_activity, ' +
        '#ensalement_priority_panel, ' +
        '#ensalement_priority_date, ' +
        '#ensalement_priority_systemEnsalementSessions', function (e) {

        let dataForm = new FormData($form[0]);

        $.ajax({
            url: $('form#priorityScheduleFilterForm').attr('action'),
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

    $(document).on('submit', 'form#priorityScheduleForm', function (e) {
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

                try {
                    $form.html($(data.responseText).html());
                } catch (e) {
                }

                $('.invalid-feedback').show();
            },
            complete: function () {
                $form.find('#btnLoad').removeClass('fa fa-spinner fa-spin');
                $form.find('select').customSelect2();
            }
        });
    });
});
