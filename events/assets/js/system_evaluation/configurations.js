let handlerForm = $('form#systemEvaluationConfig');
$(function () {
    $('#saveConfigBt').on('click', function () {
        handlerForm.submit();
    });
    $('#articleAvailableBt').on('click', function () {
        handlerForm.find('#article_free').val(1);
        handlerForm.submit();
    });
});