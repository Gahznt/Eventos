const $modal = $('#cookieconsent_modal')

$(document).on('click', '#cookieconsent_accept, #cookieconsent_deny', function (e) {
    e.preventDefault()

    $('#cookieconsent_accept, #cookieconsent_deny').prop('disabled', true).addClass('disabled')

    let $el = $(e.target)
    $el.find('.activity-indicator').addClass('fa fa-spinner fa-spin')

    $.ajax({
        url: $el.prop('href'),
        type: 'GET',
        complete: function (response, statusCode, xhr) {
            $modal.fadeOut(600, () => {
                $el.find('.activity-indicator').removeClass('fa fa-spinner fa-spin')
                $modal.remove()
            })
        }
    });
});

$(document).ready(() => {
    $modal.fadeIn(1200)
})
