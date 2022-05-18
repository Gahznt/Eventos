$(function () {

    /*$(document).on('keypress', '#formLogin', function (e) {
        if (e.which == 13) {
            $('#BtFormLogin').click();
            $('#inputIdentifier').focus();
        }
    });*/

    $(document).on('submit', '#formLoginLogin', function (e) {
        e.preventDefault();

        $('#formLoginLogin').removeClass('was-validated');
        $('#inputPassword').removeClass('is-invalid');
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('defaults', 'true');

                $('#BtFormLogin').removeClass('follow');
                $('#btnLoginLoad').removeClass('d-none');
            },
            success: function (data, statusCode, xhr) {
                let error = xhr.getResponseHeader('x-error');
                if (error) {
                    $('#btnLoginLoad').addClass('d-none')
                    $('#formLoginLogin').html(data);
                    $('#formLoginLogin').addClass('was-validated');
                    $('#inputPassword').addClass('is-invalid');
                    $('#BtFormLogin').addClass('follow');
                } else {
                    window.location.href = '/';
                }
            },
        });

        return false;
    });

    /*$(document).on('click', '#BtFormLogin', function (e) {
        e.preventDefault();

        $('#formLoginLogin').submit();
    });*/

    $(document).on('click', '#btn-password-recovery-show', function (e) {
        e.preventDefault();

        $('#formLoginLogin').addClass('d-none');
        $('#formPasswordRecovery').removeClass('d-none');
    });

    $(document).on('click', '#btn-password-recovery-dismiss', function (e) {
        e.preventDefault();

        $('#formPasswordRecovery').addClass('d-none');
        $('#formLoginLogin').removeClass('d-none');
    });

    $(document).on('submit', '#formPasswordRecovery', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function () {
                $('#btnPasswordRecoveryLoad').removeClass('d-none');
                $('#BtFormPasswordRecovery').removeClass('follow');
            },
            complete: function () {
                $('#btnPasswordRecoveryLoad').addClass('d-none');
                $('#BtFormPasswordRecovery').addClass('follow');
            },
            success: function (data, statusCode, xhr) {
                $('#formPasswordRecovery').html(data);
            },
            error: function (data) {
                $('#formPasswordRecovery').html(data.responseText);
            }
        });
    })
});

