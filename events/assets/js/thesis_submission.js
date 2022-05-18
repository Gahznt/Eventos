require('dependent-dropdown');

$(document).ready(function () {
    $('#thesis_userThemes').depdrop({
        //initDepends: ['thesis_division'],
        //initialize: true,
        depends: ['thesis_division'],
        url: $('#thesis_userThemes').data('route'),
        placeholder: $('#thesis_userThemes').data('select'),
        loading: false
    });

    // refs: https://www.w3schools.com/bootstrap4/bootstrap_forms_custom.asp
    // Add the following code if you want the name of the file appear on select
    $(document).on("change", ".custom-file-input", function (e) {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $(document).on('submit', 'form[name="thesis"]', function (e) {
        let $form = $(this);
        $form.find('[type="submit"]').prop('disabled', true);
        $form.find('#btnLoad').addClass('fa fa-spinner fa-spin');
    });
});
