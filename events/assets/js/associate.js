let $collectionHolderAditionals;

$(function () {
    $collectionHolderAditionals = $('#divisionsTable');

    const dPrim = $('input[name="associate[division]"]');
    const dSec = $('input[name="associate[aditionals][]"]');

    function setDisabled() {
        dPrim.filter(':checked').closest('tr').find('input[name="associate[aditionals][]"]').prop({
            checked: false,
            disabled: true
        });
        dSec.filter(function () {
            return !$(this).is(':checked') && $(this).val() != dPrim.filter(':checked').val();
        }).attr('disabled', dSec.filter(':checked').length == 2);
    }

    dPrim.on('change', setDisabled);
    dSec.on('change', setDisabled);

    setDisabled();

    $("#submitForm").on('click', function () {
        $("#associate").submit();
    })
});
