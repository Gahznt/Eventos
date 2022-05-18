$(function() {
    let form = $('form#systemEvaluationAveragesFormSearch');
    let saved = $('#system_evaluation_averages_saved');

    $(document).on("click","#btnSalvar",function(e) {
        e.preventDefault();
        saved.val(1);
        form.submit();
    });
});