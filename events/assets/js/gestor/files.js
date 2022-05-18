$(document).ready(function () {
    $(document).on('change', '#edition_file_file', function () {
        if (!this.files || !this.files[0] || !this.files[0].size) {
            return;
        }

        $('#filename-label').text(this.files[0].name);
        $('#btn-remove-file').removeClass('d-none');
    });

    $(document).on('click', '#btn-remove-file', function () {
        let $file = $('#edition_file_file');
        let self = $(this);

        $('#filename-label').text('Nenhum arquivo selecionado');
        self.addClass('d-none');
        $file.val(null);
    });

    $(document).on('submit', 'form#fileForm', function (e) {
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
    });

    $(document).on('click', '.copy-content', function (e) {
        e.preventDefault();

        const $self = $(this);

        let originalTitle = $self.data('original-title');
        let arrTmp = originalTitle.split(' ');
        arrTmp.shift();
        originalTitle = arrTmp.join(' ');

        $(`#${$self.attr('aria-describedby')} .tooltip-inner`).text(`${originalTitle} Copiado!`);

        const $input = $('#copy-content-input');
        $input.removeClass('d-none');

        /* Get the text field */
        var copyText = document.getElementById('copy-content-input');
        copyText.value = $self.data('content')?.trim();

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand('copy');

        copyText.value = '';
        $input.addClass('d-none');
    });
});
