$(function () {
    let $edition = $('#editionId');

    $("#editionId :selected").prop("selected", false);

    $(document).on('click', '.btnCertDownload', function (e) {
        e.preventDefault();
        let form = $(this).parent().find('form');
        form.submit();
    });

    $(document).on('click', '.deleteSubmission', function (e) {
        e.preventDefault();
        let route = $(this).data('route');
        //let title = $(this).data('title');
        let modalHandler = $('#modalRemove');
        //modalHandler.find('#title').html(title);
        modalHandler.find('#remove').prop('href', route);
        modalHandler.modal();
    });

    $(document).on('change', '#editionId', function (e) {
        $('#dashboard-actions .dash-button-md').addClass('disabled');

        let $self = $(this);
        let val = $self.val();
        let config = $self.find(':selected').data('config');

        if (val.trim() !== '' && config && !!config.config) {
            $('#newArticleSubmission').toggleClass('disabled', config.config.articeSubmissionAvaliable == 0);
            $('#btnSubActivity').toggleClass('disabled', config.config.articeSubmissionAvaliable == 0);
            $('#btnSubPainel').toggleClass('disabled', config.config.panelSubmissionAvailable == 0);
            $('#newThesisSubmission').toggleClass('disabled', config.config.thesisSubmissionAvailable == 0);

            $('#btnAvalArticle').toggleClass('disabled', config.config.evaluateArticleAvaliable == 0);
            $('#btnSignUpSections').toggleClass('disabled', config.config.freeSections == 0);
            $('#btnEditEnsalamentPriority').toggleClass('disabled', config.config.ensalementPriority == 0);
            $('#btnEditEnsalamentGeneral').toggleClass('disabled', config.config.ensalementGeneral == 0);
            $('#btnSignUpCertificates').toggleClass('disabled', config.config.freeCertiticates == 0);

            $('#btnAvalThemes').removeClass('disabled');
            $('#newThemeSubmission').removeClass('disabled');
        }
    });

    $(document).on(
        'click',
        '#btnAvalArticle, ' +
        '#btnSubActivity, ' +
        '#btnSubPainel, ' +
        '#btnSignUpSections, ' +
        '#btnEditEnsalamentPriority, ' +
        '#btnEditEnsalamentGeneral, ' +
        '#btnSignUpCertificates, ' +
        '#btnAvalThemes, ' +
        '#newArticleSubmission, ' +
        '#newThemeSubmission, ' +
        '#newThesisSubmission',
        function (e) {
            e.preventDefault();
            let route = $(this).data('route');
            let edition = $('#editionId :selected').val();
            route = route.replace(/__route__/g, edition);
            window.location.href = route;
        }
    );

    $(document).on('change', '#eventoId', function (e) {

        let $self = $(this);
        let val = $self.val();
        let $option = $self.find(`option[value="${val}"]`);

        $edition.prop('disabled', true);

        while ($edition.find('option').length > 1) {
            $edition.find('option').eq($edition.find('option').length - 1).remove();
        }

        if ($option.length === 0) {
            //$edition.select2();
            $edition.trigger('change');
            return;
        }

        let editions = $option.data('editions');

        if (editions.length > 0) {
            let locale = $option.data('locale');

            for (let i = 0; i < editions.length; i++) {
                $edition.append('<option value="' + editions[i].id + '">' + editions[i].name_i18n[locale] + '</option>');
                $edition.find('option:last').data('config', editions[i]);
            }

            $edition.prop('disabled', false);
        }

        //$edition.select2();
        $edition.trigger('change');
    });
});
