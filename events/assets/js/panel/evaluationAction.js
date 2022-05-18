$(function() {

    let step = parseInt($('#step', $('#panelListForm').val()));
    
    $(`#nav-${step}`).trigger('click');

    $(document).on('click', '#panelEvalSearchBtn', function (e) {
        e.preventDefault();
        $('#step', $('#panelListForm')).val(1);
        $('form#panelListForm').submit();
    });

    $(document).on('click', '#panelEvalActionBtn', function (e) {
        e.preventDefault();
        let eform = $('#panelActionForm');

        if ( $('#panel_evaluation_action_statusEvaluation', eform).val() == 1) {
            alert('Escolha um status para avaliar.');
            return;
        } else {
            if ( $('#panel_evaluation_action_statusEvaluation', eform).val() == 3) {
                if ( $('#panel_evaluation_action_reason', eform).val() == '') {
                    alert('Preencha um motivo para o novo status.');
                    return;
                }
            }
        }

        var disabled = eform.find(':input:disabled').removeAttr('disabled');

        $.ajax({
            url: eform.attr('data-url'),
            type: 'POST',
            data: eform.serialize(),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('defaults', 'true');                
            },
            error: function(xhr) {
                let error = xhr.getResponseHeader('x-error');
                alert( error );
            },
            success: function (data, statusCode, xhr) {
                let error = xhr.getResponseHeader('x-error');
                if (error) {
                    console.log( error );
                    disabled.attr('disabled','disabled');

                } else {
                    $('#flashSuccess', eform).val(1);
                    eform.submit();
                }
            },
        });
    });
});