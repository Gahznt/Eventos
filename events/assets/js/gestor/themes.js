$(document).ready(function () {
    $(document).on('click', '.delete-submission', function (e) {
        e.preventDefault();

        let route = $(this).data('route');
        let $modal = $('#modal-remove');
        $modal.find('#remove').prop('href', route);
        $modal.modal('show');
    });
});
