$(document).ready(function () {
    $(document).on('click', '#btn-cancel-submission', function (e) {
        e.preventDefault();

        let route = $(this).data('route');
        let $modal = $('#modal-cancellation');
        $modal.find('#btn-cancellation').prop('href', route);
        $modal.modal('show');
    });

    $(document).on('submit', '#modal-cancellation form', function (e) {
        e.preventDefault();

        var $form = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'json',
            async: true,
            data: $form.serialize(),
            beforeSend: () => {
                $form.find('.alert').remove();
                $form.find('.fa-spinner').show();
            },
            success: function (data, statusCode, xhr) {
                window.location = $form.data('redirect-route');
            },
            error: function (data) {
                if (!!data && !!data.responseJSON && !!data.responseJSON.message) {
                    $form.prepend('<div class="alert alert-danger">' + data.responseJSON.message + '</div>');
                }
            },
            complete: function () {
                $form.find('.fa-spinner').hide();
            }
        });
    });
});
