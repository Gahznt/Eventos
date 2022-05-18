require('dependent-dropdown');
require('jquery-mask-plugin');

let $collectionHolderKeyword;
let $collectionHolderAuthor;
let $collectionHolderFiles;
let $addNewItemAuthor = $('#authors-container-form');
let $themes = $('#user_articles_userThemes');
let $keywords = $('#user_articles_keywords');

let dragDropUploads = [];

function injectFiles(prev, files) {

    if (files[0].type === "application/pdf" || files[0].type === "application/x-pdf") {

        $(prev).find('input:file').files = files;
        let replace = $(prev).find('span').html();
        replace = replace.replace(/__file__/g, files[0].name);
        $(prev).find('span').html(replace);
        addRemoveButtonFile($(prev).find('span').after());
        $(prev).show();
    } else {
        $(prev).remove();
    }

    if ($('.realArticleUnique:checked').length === 0) {
        $('.realArticleUnique:last').trigger('click');
    }
}

function addNewFile(flag) {
    let handler = $('#files-container-form');
    let index = handler.find('.file').length;
    let form = handler.data('prototype');
    form = form.replace(/__name__/g, index);
    let $panel = $("<div class='file'></div>").data('index', index).hide();
    index++;
    handler.data('index', index);
    $panel = $panel.append(form);
    handler.append($panel);

    if (flag === true) {
        $panel.find('input:file').click();
    }

    return $panel;
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

function addNewFormKeyword() {

    let prototype = $collectionHolderKeyword.data('prototype');
    let index = $collectionHolderKeyword.data('index');
    let newForm = prototype;
    if (!newForm) {
        return;
    }
    newForm = newForm.replace(/__name__/g, index);

    $collectionHolderKeyword.data('index', index + 1);
    $collectionHolderKeyword.html(newForm);
}

function addNewFormAuthor() {
    let prototype = $collectionHolderAuthor.data('prototype');
    let index = $collectionHolderAuthor.data('index');
    let newForm = prototype;
    if (!newForm) {
        return;
    }
    newForm = newForm?.replaceAll('__name__', index);
    newForm = newForm?.replaceAll('@authorid', index + 1);
    index++;
    $collectionHolderAuthor.data('index', index);
    let $panel = $('<div class="autor mb-3"></div>').append(newForm);

    if (index > 1) {
        addRemoveButtonAuthor($panel.find('.fieldsetBg'));
    }

    $collectionHolderAuthor.append($panel);

    checkAuthorsCount();
}

function addRemoveButtonAuthor($panel) {

    let $removeButton = $($.parseHTML($('#author-remove').html()));
    let $panelFooterTop = $('<div class="row"></div>');
    let $panelFooter = $('<div class="col-lg-12 btAutor"></div>').append($removeButton);
    $panelFooterTop.append($panelFooter);
    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.autor').slideUp(1000, function () {
            $(this).remove();
            checkAuthorsCount();
        });
    });
    $panel.append($panelFooterTop);
}

function fixNumberWords() {
    const $self = $('#user_articles_resume');
    const maxNumberWords = $self.data('number_words') || 200;
    const initialValue = $self.val();
    const slicedValue = initialValue?.replace(/\s+/, ' ')?.split(/\s+/)?.slice(0, maxNumberWords) || [];
    const currentNumberWords = initialValue.length
        ? slicedValue.length
        : 0;
    $self.val(slicedValue.join(' '));
    $('#remaining_words_counter').text(maxNumberWords - currentNumberWords);
}

function checkAuthorsCount() {
    const length = $('#stepAutor').find('.autor').length;

    if (length >= 6) {
        $('#btAuthorAdd').prop('disabled', true).addClass('disabled');
    } else {
        $('#btAuthorAdd').prop('disabled', false).removeClass('disabled');
    }


    $collectionHolderAuthor.find('.fieldsetBg').each(function (index) {
        const $title = $(this).find('#title');
        const $order = $(this).find(`input[name*="[order]"]`);
        $title.text($title.text().replace(/\d+/, index + 1));
        $order.val(index + 1);
    });
}

function stepStart() {

    //$keywords.select2().trigger('change');
    //$('#userArticlesKeyWord').select2().trigger('change');
    $('select[name="user_articles[keywords][]"]').customSelect2();

    $('#btnLoad').removeClass('fa fa-spinner fa-spin');

    $collectionHolderFiles = $('#files-container-form');
    $collectionHolderAuthor = $('#authors-container-form');
    //$collectionHolderAuthor.append($addNewItemAuthor);
    //$collectionHolderAuthor.data('index', $('.autor').length);
    $collectionHolderFiles.data('index', $('.file').length);

    if ($collectionHolderAuthor.find('.fieldsetBg').length === 0) {
        addNewFormAuthor();
    } else {
        $collectionHolderAuthor.find('.autor').each(function (index, item) {
            const $panel = $(this);
            if (index > 0) {
                addRemoveButtonAuthor($panel.find('.fieldsetBg'));
            }
        });
    }

    $('select[name="user_articles[userThemes]"]').depdrop({
        initDepends: ['user_articles_divisionId'],
        initialize: true,
        depends: ['user_articles_divisionId'],
        url: $themes.attr('route'),
        placeholder: $themes.attr('select'),
        loading: false
    });

    $('select[name="user_articles[keywords][]"]').depdrop({
        initDepends: ['user_articles_divisionId', 'user_articles_userThemes', 'user_articles_language'],
        initialize: true,
        depends: ['user_articles_divisionId', 'user_articles_userThemes', 'user_articles_language'],
        url: $keywords.attr('route'),
        placeholder: $keywords.attr('select'),
        loading: false
    });

    fixNumberWords();
    checkAuthorsCount();
}

$(document).ready(function () {
    stepStart();

    /*$(document).on('change', '.searchIdentifier', function (e) {
        let index = $(this).data('index');
        let url = window.location.href;

        if ($(this).val() !== "31" && $(this).val() !== "") {
            $(`#label-searchValue-${index}`).html("Passport");
        } else {
            if(url.indexOf("pt_br") > -1) {
                $(`#label-searchValue-${index}`).html("CPF (somente números)");
            } else {
                $(`#label-searchValue-${index}`).html("CPF (numbers only)");
            }
        }
    });*/

    $(document).on('click', '.realArticleUnique', function (e) {
        var url = window.location.href;
        var html = $('<span class="realArticle"> Este é o artigo</span>');

        $('.realArticle').remove();
        $('.realArticleUnique').prop('checked', false);
        $(this).prop('checked', true);

        if (url.indexOf("pt_br") > -1) {
            html = $('<span class="realArticle">Este é o artigo</span>');
        } else {
            html = $('<span class="realArticle">This is the manuscript file</span>');
        }

        $(this).after(html);
    });

    $(document).on('change', '.attchments', function (e) {

        injectFiles($(e.target).parents('.file'), e.target.files)
    });

    $(document).on('click', '.userSearch', function (e) {
        e.preventDefault();

        $(this).closest('.fieldsetBg')?.find('.form-error, .invalid-feedback')?.remove();

        let index = $(this).data('index');
        let value = $(`input[name*='[${index}][searchValue]'`)?.val()?.trim();
        let identifier = $(`select[name*='[${index}][searchIdentifier]'] option:selected`)?.val()?.trim();

        $(`input[name*='[${index}][userAuthorId]'`).val('');
        $(`input[name*='[${index}][userAuthorIdFake]'`).val('');

        $.ajax({
            url: $(this).data('route'),
            type: 'GET',
            data: {identifier: identifier, value: value},
            success: function (data) {
                if (!data) {
                    return;
                }

                if (data.error) {
                    $(`input[name*='[${index}][userAuthorIdFake]'`).val(data.error);
                    return;
                }

                if (!data.id) {
                    return;
                }

                let id = data.id;
                let name = data.name;

                if ($(`input[name*="[userAuthorId]"][value="${id}"]`).length > 0) {
                    $(`input[name*='[${index}][userAuthorIdFake]'`).val('Já adicionado.');
                } else {
                    $(`input[name*='[${index}][userAuthorId]'`).val(id);
                    $(`input[name*='[${index}][userAuthorIdFake]'`).val(name);
                }
            },
            error: function () {
                $(`input[name*='[${index}][userAuthorIdFake]'`).val('Não encontrado.');
            }
        });
    });

    $(document).on('click', '#btAuthorAdd', function (e) {
        const length = $('#stepAutor').find('.autor').length;
        if (length >= 6) {
            return;
        }

        addNewFormAuthor();
    });

    $(document).on('submit', 'form#formArticleStep', function (e) {
        e.preventDefault();
        $('.invalid-feedback').html('');
        $('form#formArticleStep').removeClass('was-validated');

        $('.invalid-feedback').hide();

        let dataForm = new FormData(this);

        let f;
        let size = 0;

        if (dragDropUploads.length > 0) {
            for (f = 0; f < dragDropUploads.length; f++) {

                size = size + dragDropUploads[f][0].size;
                dataForm.append(`user_articles[userArticlesFiles][${f}][path]`, dragDropUploads[f][0]);

            }
        } else {
            for (f = 0; f <= $('.file').length; f++) {

                let list = $(`input[name='user_articles[userArticlesFiles][${f}][path]']`);
                for (i = 0; i <= list.length; i++) {

                    if ($(list[i])[0] && $(list[i])[0].files[0]) {
                        size = size + $(list[i])[0].files[0].size;
                    }
                }
            }
        }

        if ((size / (1024 * 1024)).toFixed(2) > 2.00) {
            $('.invalid-feedback').append('Files over size');
            $('.invalid-feedback').show();
            $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            return false;
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            success: function (data, statusCode, xhr) {
                let handler = $("#articleForm");
                let step = xhr.getResponseHeader('x-step');
                let last_step = step;
                --last_step;

                $('#btnLoad').removeClass('fa fa-spinner fa-spin');

                if (data.saved === true && data.pass === true) {

                    if (step) {
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        $(`#articleTimeLine .step`).removeClass('active').addClass('complete');
                        handler.find('.stepsTab').hide();
                        handler.find(".stepsTab[data-step=5]").show();
                        $('#step').val(5);
                        $('.btCadastroVoltar').hide();
                        $('.btCadastro').hide();
                        $('#btnLoad').removeClass('fa fa-spinner fa-spin');
                    }

                }

                if (data.saved === false && data.pass === true) {

                    if (step) {

                        handler.find(".stepsTab").hide();
                        handler.find(`.stepsTab[data-step=${last_step}]`).find('.form-error').html("");
                        handler.find(`.stepsTab[data-step=${step}]`).show();
                        handler.find('#step').val(step);

                        let handlerStep = $('#step');

                        $('.stepsTab').hide();
                        $(`#articleTimeLine .step[data-step="${last_step}"]`).removeClass('active').addClass('complete');
                        $(`#articleTimeLine .step[data-step="${step}"]`).addClass('active');

                        handlerStep.val(step);
                        $('.btCadastroVoltar').removeClass('disabled').show();
                        $(`.stepsTab[data-step=${step}]`).show();

                    }
                }

                $('.mobileSteps span').text(step);
            },
            error: function (data) {
                $('form#formArticleStep').addClass('was-validated');
                let step = data.getResponseHeader('x-step');
                $('#step').val(step);
                $("#articleForm").html(data.responseText).find(`.stepsTab[data-step=${step}]`).show();
                $('.invalid-feedback').show();
                stepStart();
            },
            complete: function () {
                $('#btnLoad').removeClass('fa fa-spinner fa-spin');
            }
        });
    });

    // Begin Controll navegation form
    let step = parseInt($('#step').val());

    $('.btCadastroVoltar').hide();

    $(`.stepsTab[data-step=${step}]`).show();

    $(document).on('click', '.btCadastro', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 4) {
            $(this).addClass('disabled');
        }

        $('#btnLoad').addClass('fa fa-spinner fa-spin');
        $('form#formArticleStep').submit();
    });

    $(document).on('click', '.btCadastroVoltar', function () {
        let handlerStep = $('#step');
        let step = parseInt(handlerStep.val());

        if (step > 1) {
            $(this).removeClass('disabled').show();
            $('.stepsTab').hide();
            $(`#articleTimeLine .step[data-step="${step}"]`).removeClass('active').removeClass('complete');
            --step;
            $(`#articleTimeLine .step[data-step="${step}"]`).addClass('active').removeClass('complete');
            $(`.stepsTab[data-step=${step}]`).show();
            handlerStep.val(step);
        }

        if (step === 1 || step > 4) {
            $(this).addClass('disabled').hide();
        }

        $('.mobileSteps span').text(step);
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
        let handler = addNewFile(false);
        let index = handler.data('index');
        dragDropUploads[index] = files;
        injectFiles(handler, files);
    };

    $(document).on('keyup', '#user_articles_resume', function (e) {
        fixNumberWords();
    });

    $(document).on('change', '#user_articles_jobComplete', function (e) {
        const $self = $(this);
        $('#user_articles_resumeFlag').prop('checked', !$self.prop('checked'));
    });

    $(document).on('change', '#user_articles_resumeFlag', function (e) {
        const $self = $(this);
        $('#user_articles_jobComplete').prop('checked', !$self.prop('checked'));
    });
});
