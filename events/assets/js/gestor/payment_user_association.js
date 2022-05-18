let $collectionHolderFiles;

let dragDropUploads = [];

function injectFiles(files) {
    if (files[0].type === "text/plain") {

        $('#payment_user_association_file').files = files;

        let handler = $('#files-container-form');
        let index = handler.find('.file').length;
        let $panel = $("<div class='file'></div>").data('index', index);
        dragDropUploads[index] = files;
        index++;
        handler.data('index', index);
        $panel = $panel.append(`<span id="title">${files[0].name}</span>`);
        addRemoveButtonFile($panel.find('span').after());
        handler.append($panel);
    } else {
        $(prev).remove();
    }

    if ($('.realArticleUnique:checked').length === 0) {
        $('.realArticleUnique:last').trigger('click');
    }
}

function addNewFile(flag) {
    if (flag === true) {
        $('#payment_user_association_file').click();
    }
}

function addRemoveButtonFile($panel) {
    let $removeButton = $($.parseHTML($('#file-remove').html()));

    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.file').slideUp(400, function () {
            let index = $(this).data('index');
            dragDropUploads.splice(index, 1);
            $(this).remove();

            if ($('.realArticleUnique:checked').length === 0) {
                $('.realArticleUnique:first').trigger('click');
            }
        })
    });

    $panel.append($removeButton);
}

function stepStart() {

    $('#btnLoad').removeClass('fa fa-spinner fa-spin');

    $collectionHolderFiles = $('#files-container-form');
    $collectionHolderFiles.data('index', $('.file').length);
}

$(document).ready(function () {
    stepStart();

    $(document).on('change', '.attachments', function (e) {
        injectFiles(e.target.files)
    });

    $(document).on('submit', 'form#formPaymentUserAssociation', function (e) {
        e.preventDefault();
        $('.invalid-feedback').html('');
        $('form#formArticleStep').removeClass('was-validated');

        $('.invalid-feedback').hide();

        let dataForm = new FormData(this);

        const input = document.querySelector('#payment_user_association_file');

        if (input.files.length === 0) {
            $('.invalid-feedback').append('Selecione um arquivo antes de continuar');
            $('.invalid-feedback').show();
            $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            return false;
        }

        dataForm.append(`file`, input.files[0]);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            success: function (data, statusCode, xhr) {
                window.location = $('#manager-back-route').prop('href');
            },
            error: function (data) {
                $('form#formPaymentUserAssociation').addClass('was-validated');
                $('.invalid-feedback').show();
                // stepStart();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    });

    $(document).on('click', '.fileDropzone', function (e) {
        e.preventDefault();
        addNewFile(true);
    });

    let holder = document.getElementById('articleDropzone');
    holder.ondragover = function () {
        this.className = 'hover';
        return false;
    };
    holder.ondrop = function (e) {
        this.className = 'hidden';
        e.preventDefault();
        let files = e.dataTransfer.files;
        addNewFile(false);
        injectFiles(files);
    };
});
