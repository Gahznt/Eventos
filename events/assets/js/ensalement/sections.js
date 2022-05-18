$(document).ready(function () {

    const onFormLoad = () => {
        $('#articles_prototype').data('index', $('#articles_prototype').find('.collection-item').length);

        $('#ensalement_section__article').trigger('change');
    }

    $(document).on('click', '.btn-remove-article', function (e) {
        e.preventDefault();

        let $self = $(this);

        if (confirm('Tem certeza que deseja remover este artigo?')) {
            $self.closest('.collection-item').fadeOut(() => {

                setTimeout(() => {
                    $self.closest('.collection-item').remove();

                    if ($('#articles_prototype').find('.collection-item').length === 0) {
                        $('#articles_prototype').addClass('d-none');
                        $('#articles_empty').removeClass('d-none');
                    }
                }, 100);
            });
        }
    });

    $(document).on('click', '.btn-add-article', function (e) {
        let $option = $('#ensalement_section__article').find('option:selected');

        $('#article_exists').hide().addClass('d-none');

        let exists = false;
        $('#articles_prototype').find('.collection-item').each((index, item) => {
            exists = exists || $(item).find('[name$="[userArticles]"]').val() === $option.val();
        });

        if (exists) {
            $('#article_exists').removeClass('d-none').show();
            return;
        }

        $('#articles_empty').addClass('d-none');
        $('#articles_prototype').removeClass('d-none');

        let index = $('#articles_prototype').data('index');
        let newItem = $('#articles_prototype').data('prototype').replace(/__name__/g, index);

        let $newItem = $(newItem);

        // input
        $newItem.find('#ensalement_section_articles_' + index + '_userArticles').val($option.val());
        $newItem.find('#ensalement_section_articles_' + index + '_articleTitleFake').val($option.text());

        // label
        $newItem.find('.article-title-fake').text($option.text());

        $('#articles_prototype').find('.collection-items').append($newItem);
        $('#articles_prototype').data('index', ++index);
    });


    let $form = $('form#sectionScheduleForm');

    $form.find('select').customSelect2();

    $(document).on('change', '#ensalement_section_date', function (e) {
        let $self = $(this);
        $('#ensalement_section_systemEnsalementSessions').prop('disabled', true).val('').customSelect2();
        $('#ensalement_section_systemEnsalementSlots').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_section_systemEnsalementSessions', function (e) {
        let $self = $(this);
        $('#ensalement_section_systemEnsalementSlots').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_section_date, ' +
        '#ensalement_section_systemEnsalementSessions', function (e) {
        $form.find('#activity_indicator_slot').removeClass('d-none');
    });

    $(document).on('change', '#ensalement_section_division', function (e) {
        let $self = $(this);
        $('#ensalement_section_userThemes').prop('disabled', true).val('').customSelect2();
        $('#ensalement_section__article').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_section_userThemes', function (e) {
        let $self = $(this);
        $('#ensalement_section__article').prop('disabled', true).val('').customSelect2();
    });

    $(document).on('change', '#ensalement_section__article', function (e) {
        $('#article_exists').hide().addClass('d-none');

        let $self = $(this);
        $('.btn-add-article').prop('disabled', $self.val().trim() === '');
    });

    $(document).on('change', '#ensalement_section_division, ' +
        '#ensalement_section_userThemes', function (e) {
        $form.find('#activity_indicator_article').removeClass('d-none');
    });

    onFormLoad();

    $(document).on('change', '#ensalement_section_date, ' +
        '#ensalement_section_systemEnsalementSessions, ' +

        '#ensalement_section_division, ' +
        '#ensalement_section_userThemes', function (e) {

        let dataForm = new FormData($form[0]);

        $.ajax({
            url: $('form#sectionScheduleFilterForm').attr('action'),
            type: 'POST',
            processData: false,
            contentType: false,
            data: dataForm,
            beforeSend: () => {
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
            },
            success: function (data, statusCode, xhr) {
                $form.html($(data).html());
                onFormLoad();
            },
            error: function (data) {
                $form.addClass('was-validated');

                $form.html($(data.responseText).html());
                onFormLoad();

                $('.invalid-feedback').show();
            },
            complete: function () {
                $form.find('.activity-indicator').addClass('d-none');
                $form.find('select').customSelect2();
            }
        });
    });

    $(document).on('submit', 'form#sectionScheduleForm', function (e) {
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
                $form.removeClass('was-validated');
                $('.invalid-feedback').html('');
                $('.invalid-feedback').hide();
                $form.find('#btnLoad').addClass('fa fa-spinner fa-spin');
            },
            success: function (data, statusCode, xhr) {
                if ($('#back-route').length > 0) {
                    window.location = $('#back-route').prop('href');
                } else {
                    window.location.reload();
                }
            },
            error: function (data) {
                $form.addClass('was-validated');

                try {
                    $form.html($(data.responseText).html());
                    onFormLoad();
                } catch (e) {
                }

                $('.invalid-feedback').show();
            },
            complete: function () {
                $form.find('#btnLoad').removeClass('fa fa-spinner fa-spin');
                $form.find('select').customSelect2();
            }
        });
    });
});
