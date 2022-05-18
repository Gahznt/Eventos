$(document).ready(function () {
    $('.date').mask('00/00/0000');

    $(document).on('click', '#userFormSearchBtn', function (e) {
        e.preventDefault();
        $('form#userFormSearch').submit();
    });

    $('#user_association_division').on('change', function () {
        let option = $("#user_association_division").val();

        $(`#user_association_aditionals option`).prop('selected', false).prop('disabled', false);
        $(`#user_association_aditionals option[value="${option}"]`).prop('disabled', true);

        $('#user_association_aditionals').trigger('change');

        //$('.bs-multiselect').customSelect2();
    });

    $('#user_association_division').trigger('change');

    const associacoes = $('[data-associacao]').map(function () {
        return JSON.parse(decodeURIComponent($(this).data('associacao')));
    }).toArray();

    $('.tbEditAssociacao').on('click', function () {
        const associacao = associacoes.find(a => a.id == $(this).data('id'));
        if (associacao) {
            $('#user-nav-3').tab('show');
            $('#editAssociacao input[name="id"]').val(associacao.id);
            $('#editAssociacao select').each(function () {
                $(this).val($(this).find(`option[value="${associacao[$(this).attr('name')]}"]`).length > 0 ? associacao[$(this).attr('name')] : '');
            });
        }
    });

    $('#user-nav-tab a').on('hide.bs.tab', function (e) {
        if (e.currentTarget.id == 'user-nav-3') {
            $('#editAssociacao').find('input, select').val('');
        }
    });

    if ($('#tabIdentifier').length && $('#tabIdentifier').val() == 'new') {
        $('.tbEditAssociacao').click();
        $('#user-nav-3').tab('show');
    }
    if ($('#tabIdentifier').length && $('#tabIdentifier').val() == 'list') {
        $('#user-nav-2').tab('show');
    }

});

$.fn.selectBoxPopulate = function (data) {
    return this.each(function () {
        var $container = $(this);

        while ($container.find('option').length > 1) {
            $container.find('option').eq($container.find('option').length - 1).remove();
        }

        if (Object.keys(data).length > 0) {
            for (i in data) {
                $container.append('<option value="' + i + '">' + data[i] + '</option>');
            }
        }
    });
};

$(document).ready(function () {
    $(document).on('change', '[name$="[institution]"]', function (e) {
        const $self = $(this);

        const $panel = $self.closest('.jquery_institution_programs_row');

        const $program = $panel.find('[name$="[program]"]');

        //$self.select2();
        const route = $self.data('route');
        const selected = parseInt($self.val());

        const $programsLoad = $panel.find('.programs-load');

        $panel.find('.other_institution_wrapper').toggleClass('d-none', selected !== 99999);
        $program.selectBoxPopulate([]);

        $.ajax({
            url: route,
            type: 'GET',
            data: {
                institution: selected
            },
            beforeSend: function () {
                $programsLoad.removeClass('d-none');
            },
            success: function (response, statusCode, xhr) {
                $program.selectBoxPopulate(response);
            },
            complete: function () {
                if (selected === 99999) {
                    $program.val(99999);
                }

                $program.trigger('change');
                $programsLoad.addClass('d-none');
            }
        });
    });

    $(document).on('change', '[name$="[program]"]', function (e) {
        const $self = $(this);

        const $panel = $self.closest('.jquery_institution_programs_row');

        $panel.find('.other_program_wrapper').toggleClass('d-none', parseInt($self.val()) !== 99999);
    });
});


