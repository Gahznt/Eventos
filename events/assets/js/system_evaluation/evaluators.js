$(function () {
    $(document).on('click', '#evaluatorsListBtn', function (e) {
        e.preventDefault();
        $('#evaluatorsFormSearch').submit();
    });
});