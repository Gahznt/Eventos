require('select2');

$(document).ready(function () {
    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        return formatRepoSelection(repo);
    }

    function formatRepoSelection(repo) {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "   <div class='select2-result-repository__meta'>" +
            "       <div class='select2-result-repository__title'></div>" +
            "       <div class='select2-result-repository__description' style='font-size: .75rem; line-height: 1.2;'></div>" +
            "   </div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.text);
        $container.find(".select2-result-repository__description").html(repo.institutionsPrograms);

        return $container;
    }

    $.fn.addCustomSelect2 = function () {
        return this.each(function () {
            var $select = $(this);
            $select.select2({
                ajax: {
                    url: $select.data('api-url'),
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 4,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        });
    };

    $('select[name$="[researcher]"]').addCustomSelect2();

    $('#researchers-container-form').on('g-collection-add-researchers', function () {
        $(this).find('.g-collection-fieldset:last select[name$="[researcher]"]').addCustomSelect2();
    });
});
