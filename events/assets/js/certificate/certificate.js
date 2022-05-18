$(function() {
    $(document).on('click', '#certificateSearch', function (e) {
        e.preventDefault();
        $('form#certificateListForm').trigger('submit');
    });

    $(document).on('click', '.setAction', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'action',
            type: 'POST',
            data: {'listId': $(this).attr('data-id')},
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
                    $('form#certificateListForm').trigger('submit');
                }
            },
        });
    });

    $(document).on('click', '#cpfSearch', function (e) {
        e.preventDefault();
        
        let cpf = $('#certificate_new_search').val();
        let country = 31; // Fixo BR

        $('#name').val('');
        $('#userId').val('');
        $.ajax({
            url: $(this).data('route'),
            type: 'GET',
            data: {countryId: country, cpf: cpf},
            success: function (response, statusCode, xhr) {
                $('#name').val(response.data.name);
                $('#userId').val(response.data.id);
            },
            error: function () {
                $('#name').val('Não econtrado');
                $('#userId').val('');
            }
        });
    });

    $(document).on('click', '.certView', function (e) {
        e.preventDefault();

        $('#divCertView').html('');

        $.ajax({
            url: 'view',
            type: 'GET',
            data: {'listId': $(this).attr('data-id')},
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
                    $('#divCertView').html( data );
                }
            },
        });
    });

    $(document).on('click', '.certDownload', function (e) {
        e.preventDefault();
        let ident = $(this).data('ident');

        if (! confirm('Deseja fazer o download deste certificado?')) {
            return;
        }

        let data = {listId: $(this).attr('data-id')};
        fetch('download', {
            cache: "reload",
            method: 'post',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
            body: $.param(data)
        })
        .then(resp => resp.blob())
        .then(blob => {
            const date = new Date();
            const file = new Blob([blob], {type: 'application/pdf'});
            const url = window.URL.createObjectURL(file);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'CERT_'+ ident + '_' + date.getTime() + '.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .then(resp => {
            $('form#certificateListForm').trigger('submit');
        })
        .catch( err => {
            console.log(err);
          })
        ;
    });
});