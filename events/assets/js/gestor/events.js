$(document).ready(function () {
    $(document).on('submit', 'form#eventoForm', function (e) {
        e.preventDefault();

        let $form = $(this);

        let dataForm = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            beforeSend: () => {
                $form.find('.card-header').removeClass('invalid').removeClass('valid');
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $('#btnLoad').addClass('fa fa-spinner fa-spin');
            },
            success: function (data, statusCode, xhr) {
                // alert('Dados gravados com sucesso.');
                window.location = $('#manager-back-route').prop('href');
            },
            error: function (data) {
                $form.addClass('was-validated');

                $form.html($(data.responseText).html());

                $form.find('.card-header').addClass('valid');
                $form.find('.form-error').closest('.card').find('.card-header').removeClass('valid').addClass('invalid');

                tinymce.remove();
                tinymce.init({
                    selector: 'textarea.htmleditor',
                    language: 'pt_BR',
                    height: 300,
                    menubar: false,
                    plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        'searchreplace wordcount visualblocks visualchars code fullscreen',
                        'insertdatetime media nonbreaking save table directionality',
                        'emoticons template paste textpattern imagetools'
                    ],
                    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
                    image_advtab: true,
                    init_instance_callback: function (editor) {
                        editor.on('input', function (e) {
                            $(`#${e.target.dataset.id}`).val(tinymce.get(e.target.dataset.id).getContent()).change();
                        });
                        editor.on('change', function (e) {
                            $(`#${e.target.id}`).val(tinymce.get(e.target.id).getContent()).change();
                        });
                    }
                });

                $('.invalid-feedback').show();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    })
});
