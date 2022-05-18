$(function() {

    let step = parseInt($('#step', $('#panelViewForm').val()));
    
    $(`#nav-${step}`).trigger('click');

    $(document).on('click', '#linkSpreadsheet', function (e) {
        e.preventDefault();
        if (! confirm('Deseja gerar a planilha de submissão de painéis?')) {
            return;
        }

        fetch('spreadsheet',
            {method: 'POST'}
        )
        .then(resp => resp.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'panelSubmission.xls';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch( err => {
            err.text().then( errorMessage => {
              this.props.dispatch(displayTheError(errorMessage))
            })
          })
        ;
    });

    $(document).on('click', '#panelEvalSearchBtn', function (e) {
        e.preventDefault();
        $('#step', $('#panelListForm')).val(2);
        $('form#panelListForm').submit();
    });

    $(document).on('click', '.panelCancel', function (e) {
        e.preventDefault();
        if (! confirm('Deseja cancelar a submissão deste painel?')) {
            return;
        }

        $.ajax({
            url: 'cancel',
            type: 'POST',
            data: {'panelId': $(this).attr('data-id')},
            beforeSend: function(xhr) {
                xhr.setRequestHeader('defaults', 'true');
            },
            error: function(xhr) {
                let error = xhr.getResponseHeader('x-error');
                console.log( error );
            },
            success: function (data, statusCode, xhr) {
                let error = xhr.getResponseHeader('x-error');

                if (error) {
                    console.log( error );
                } else {
                    $('#step', $('#panelListForm')).val(2);
                    $('form#panelListForm').submit();
                }
            },
        });

    });

    $(document).on('click', '#sendConsiderationBtn', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'consideration',
            type: 'POST',
            data: {
                'panelId': $('#panelId').val(),
                'panelConsideration': $('#panelConsideration').val(),
                'panelConsiderationAuthor': ( $('#panelConsiderationAuthor').prop('checked') ? 1 : 0)
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('defaults', 'true');
            },
            error: function(xhr) {
                let error = xhr.getResponseHeader('x-error');
                console.log( error );
            },
            success: function (data, statusCode, xhr) {
                let error = xhr.getResponseHeader('x-error');

                if (error) {
                    console.log( error );
                } else {
                    $('#step', $('#panelViewForm')).val(3);
                    $('#stepDet', $('#panelViewForm')).val(4);
                    $('form#panelViewForm').submit();
                }
            },
        });
    });

    $(document).on('click', '.panelDownload', function (e) {
        e.preventDefault();

        let downFile = $(this).attr('data-file');

        fetch('download', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
            body: JSON.stringify({
                'file': downFile
            })
        })
        .then(resp => resp.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = downFile;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch( err => {
            err.text().then( errorMessage => {
              this.props.dispatch(displayTheError(errorMessage))
            })
          })
        ;
    });    
});