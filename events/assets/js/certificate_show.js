$(window).resize(function () {
    let $container = $('#certificate-html-container');
    let $back = $container.find('div.back');

    let containerWidth = parseInt($container.width());
    let backWidth = $back.data('width');

    let finalWidth = (containerWidth * 100 / backWidth).toFixed(5);

    $back.css('zoom', finalWidth + '%');
});

$(document).ready(function () {
    let $container = $('#certificate-html-container');
    let $back = $container.find('div.back');
    let backWidth = parseInt($back.css('width'));

    $back.data('width', backWidth);

    $(window).trigger('resize');
});
