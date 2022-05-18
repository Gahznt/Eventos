$(document).ready(function () {

    $(document).on('click', '#slotsCheckAll', function (e) {
        const checked = $(this).prop('checked');

        $('.slotsCheckboxes :checkbox').prop('checked', checked);
    });

    $(document).on('change', '#system_ensalement_slots_date', function (e) {
        let $form = $('form#slotsForm');

        let dataForm = new FormData($form[0]);

        $.ajax({
            url: $('form#slotsFormSessions').attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            beforeSend: () => {
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $form.find('#sessionsLoad').removeClass('d-none');
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
                $form.find('#sessionsLoad').addClass('d-none');
            }
        });
    });

    $(document).on('submit', 'form#slotsForm', function (e) {
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
            }
        });
    })

    $(document).on('click', '.edit-link', function (e) {
        e.preventDefault();

        const data = $(this).data();
        const row = $(this).closest('tr');
        let msg = row.find(".modal-confirm-edit").html();
        let link = row.find(".modal-link-edit").html();

        $('#link').val(link.length > 0 ? link : '');
        $('#modal-msg-edit-1').text("INCLUIR/EDITAR LINK PARA O SLOT: ");
        $('#modal-msg-edit-2').text(msg);

        $('#editConfirm').on('click', function() {
            const url = data.path;
            const linkVal = $('#link').val();

            console.log(url);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    link: linkVal,
                }
            }).done(function () {
                console.log('done');
                $('#modalEdit').modal('hide');
                location.reload();
            }).fail(function () {
                alert('Erro na atualização!');
                $('#modalEdit').modal('hide');
            })
        })
    })


});
