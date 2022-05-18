require('dependent-dropdown');

$(function () {
    $(document).on('click', '#btnAvalSubmission', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnSubAtividades', function (e) {
        e.preventDefault();
        let edition = $('#editionId :selected').val();
        let route = $(this).data('route');
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnSections', function (e) {
        e.preventDefault();
        let edition = $('#editionId :selected').val();
        let route = $(this).data('route');
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnEnsalment', function (e) {
        e.preventDefault();
        let edition = $('#editionId :selected').val();
        let route = $(this).data('route');
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnSubAtividades', function (e) {
        e.preventDefault();
        let edition = $('#editionId :selected').val();
        let route = $(this).data('route');
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    /*$(document).on('click', '#btnSubTemas', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });*/

    $(document).on('click', '#btnSubPainel', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnAverages', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnConfig', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnIndications', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let edition = $('#editionId :selected').val();
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $(document).on('click', '#btnAvalArticle', function (e) {
        e.preventDefault();
        let edition = $('#editionId :selected').val();
        let route = $(this).data('route');
        route = route.replace(/__route__/g, edition);
        window.location.href = route;
    });

    $('#editionId').depdrop({
        depends: ['eventId'],
        url: $('#editionId').data('route'),
        loading: false,
        placeholder: $('#editionId').data('select'),
    });

    $(document).on('change', '#editionId', function (e) {

        $('#dashboard-actions .dash-button-md').addClass('disabled');

        let option = $('#editionId :selected').val();

        if (option) {
            $('#dashboard-actions .dash-button-md').removeClass('disabled');
        }
    });
});
