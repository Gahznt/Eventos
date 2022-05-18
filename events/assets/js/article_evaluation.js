$(function () {

    $(document).on('click', '#btnInfoAval', function (e) {
        e.preventDefault();
        $("#modalInfoAval").modal();
    });

   $(document).on('click', '#btnRemoveAval', function (e) {
        e.preventDefault();
        $("#modalRemoveAval").modal();
   });

    $(document).on('click', '#userFormSearchBtn', function (e) {
        e.preventDefault();
       $('#articleEvaluationFormSearch').submit();
    });

    $(document).on('click', '#btnSaveRemoveAval', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let justificationRemoveAval = $('#justificationRemoveAval').val();
        $.post( route, { justificationRemoveAval: justificationRemoveAval} )
            .done(function(response) {
            window.location.href = response.data.url;
             })
            .fail(function(response) {
                window.location.href = response.data.url;
            });
        $("#modalRemoveAval").modal('hide');
    });

    $(document).on('click', '#btnSaveInfoAval', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        let justificationInfoAval = $('#justificationInfoAval').val();
        $.post( route, { justificationInfoAval: justificationInfoAval})
            .done(function(response) {
                 window.location.href = response.data.url;
            })
            .fail(function(response) {
                window.location.href = response.data.url;
        });
        $("#modalInfoAval").modal('hide');
    });

});