$(document).ready(function () {
    $('.schedule').find('select[name="ensalement_general[systemEnsalementSlots]"]').customSelect2('destroy');

    $('.schedule').find('select').each((index, item) => {
        let $select = $(item);
        $select.data('old-val', $select.val());
    });

    let resultTimeout = setTimeout(() => {
    }, 100);

    $('.schedule').find('select[name="ensalement_general[systemEnsalementSlots]"]').on('change', function (e) {
        let $self = $(this);

        if (!confirm('Tem certeza que deseja alterar o Slot?')) {
            $self.val($self.data('old-val'));
            return;
        }

        let slot = $self.val();

        let $parent = $self.closest('td');

        let id = $parent.find('input[name="id"]').val().trim();

        if ('' !== slot && '' !== id) {
            $.ajax({
                url: $('#generalScheduleEditForm').attr('action').replace('__id__', id),
                type: 'POST',
                dataType: 'json',
                data: {
                    ensalement_general: {
                        systemEnsalementSlots: slot
                    }
                },
                async: true,
                beforeSend: function (jqXHR, settings) {
                    $parent.find('.activity-indicator').removeClass('d-none');
                },
                complete: function (jqXHR, textStatus) {
                    $parent.find('.activity-indicator').addClass('d-none');
                    clearTimeout(resultTimeout);
                    resultTimeout = setTimeout(() => {
                        $parent.find('.success-indicator').addClass('d-none');
                        $parent.find('.error-indicator').addClass('d-none');
                        $parent.find('.errormessage-indicator').text('');
                    }, 5000);
                },
                success: function (data, textStatus, jqXHR) {
                    $self.data('old-val', $self.val());

                    $parent.find('.success-indicator').removeClass('d-none');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $self.val($self.data('old-val'));

                    $parent.find('.error-indicator').removeClass('d-none');
                    if (jqXHR.responseJSON && jqXHR.responseJSON.systemEnsalementSlots) {
                        $parent.find('.errormessage-indicator').text(jqXHR.responseJSON.systemEnsalementSlots);
                    }
                }
            });
        }
    });

    let $form = $('form#generalScheduleSearchForm');

    $form.find('select').customSelect2();

    $(document).on('change', '#search_division', function (e) {
        let $self = $(this);
        $('#search_theme').prop('disabled', true).val('').customSelect2();
        $('#search_article').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#search_theme', function (e) {
        let $self = $(this);
        $('#search_article').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#search_date', function (e) {
        let $self = $(this);
        $('#search_time').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#search_date, ' +
        '#search_division, ' +
        '#search_theme', function (e) {

        let dataForm = new FormData($form[0]);

        $.ajax({
            url: $('form#generalScheduleSearchFilterForm').attr('action'),
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
