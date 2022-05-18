$(document).ready(function () {
    jQuery.expr[':'].icontainsX = function (a, i, m) {
        return a.innerText && jQuery(a).text().toLowerCase().indexOf(m[3].toLowerCase()) > -1;
    };

    function resetContent() {
        $('#detailed-event-schedule .search-collapse .nav-by-scheduling-header').each(function () {
            const $this = $(this);
            $this.find('.visible').html($this.find('.invisible').html().replace('/\s+/g', ' '));
        });

        $('#detailed-event-schedule .search-collapse .nav-by-scheduling-body').each(function () {
            const $this = $(this);
            $this.find('.visible').html($this.find('.invisible').html().replace('/\s+/g', ' '));
        });
    }

    resetContent();

    // Returns a function, that, as long as it continues to be invoked, will not
    // be triggered. The function will be called after it stops being called for
    // N milliseconds. If `immediate` is passed, trigger the function on the
    // leading edge, instead of the trailing.

    var debounce = function (func, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            }, wait);
            if (immediate && !timeout) func.apply(context, args);
        };
    };

    let animating = false;
    $('input[name="s"]').bind('keyup', debounce(function (e) {

        $('input[name="s"]').not(this).val('');

        // let selector = $(this).data('selector');
        let selector = '#detailed-event-schedule';

        $(selector + ' .nav-by-division-header .count').remove();

        $(selector + ' .nav-by-scheduling-header').addClass('collapsed').attr('aria-expanded', 'false');
        $(selector + ' .nav-by-scheduling-header').removeClass('activeX');
        $(selector + ' .nav-by-scheduling-body').removeClass('show');

        $(selector + ' .nav-by-division-date-header.nav-by-scheduling-header').addClass('collapsed').attr('aria-expanded', 'false');
        $(selector + ' .nav-by-division-date-body').removeClass('show');

        resetContent();

        let searchTerm = $.trim($(this).val());

        if (searchTerm.length < 3) {
            return;
        }

        if (searchTerm !== '') {
            $(selector + ' .search-collapse .nav-by-scheduling-header .visible b:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-header .visible em:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-header .visible strong:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-header .visible span:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-header .visible p:icontainsX("' + searchTerm + '")')

                .add(selector + ' .search-collapse .nav-by-scheduling-body .card-body .visible b:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-body .card-body .visible em:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-body .card-body .visible strong:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-body .card-body .visible span:icontainsX("' + searchTerm + '")')
                .add(selector + ' .search-collapse .nav-by-scheduling-body .card-body .visible p:icontainsX("' + searchTerm + '")')
                .each(function () {
                    const $this = $(this);

                    if (!this.innerHTML) {
                        return;
                    }

                    let txt = this.innerHTML;
                    let i = txt.toLowerCase().indexOf(searchTerm.toLowerCase());

                    if (i < 0) {
                        return;
                    }

                    let t = txt.substring(i, i + searchTerm.length);

                    $this.html(this.innerHTML.replace(new RegExp(t, 'ig'), '<span class="highlight" style="background: #FFFF00; border-radius: 5px; color: #000000; display: inline-block; padding: 0 7px; line-height: 160%;">' + t + '</span>'));

                    let refDivisionDate = $this.closest('.nav-by-scheduling-header').data('ref-division-date') || $this.closest('.nav-by-scheduling-body').data('ref-division-date');
                    let refScheduling = $this.closest('.nav-by-scheduling-header').data('ref-scheduling') || $this.closest('.nav-by-scheduling-body').data('ref-scheduling');

                    if (!refScheduling) {
                        return;
                    }

                    $(selector + ' .nav-by-division-date-header.' + refDivisionDate).removeClass('collapsed').attr('aria-expanded', 'true');
                    $(selector + ' .nav-by-division-date-body.' + refDivisionDate).addClass('show');

                    $(selector + ' .nav-by-scheduling-header.' + refScheduling).removeClass('collapsed').attr('aria-expanded', 'true');
                    $(selector + ' .nav-by-scheduling-header.' + refScheduling).addClass('activeX');
                    $(selector + ' .nav-by-scheduling-body.' + refScheduling).addClass('show');
                });
        }

        if (!animating) {
            animating = true;

            $(selector + ' .nav-by-division-body').each(function () {
                const $this = $(this);

                let refDivision = $this.data('ref-division');

                if (!refDivision) {
                    return;
                }

                let count = $this.find('.activeX').length;
                if (count) {
                    $(selector + ' .nav-by-division-header.' + refDivision).append('<span class="count" style="position: absolute;top: -5px;right: -5px;border-radius: 100px;padding: 3px;min-width: 16px;background: #FFFF00;color: #000000;font-size: 80%;line-height: 10px;">' + count + '</span>');

                    if (!$(selector + ' .nav-by-division-body.active').find('.activeX').length) {
                        $(selector + ' .nav-by-division-header').removeClass('active').attr('aria-selected', 'false');
                        $(selector + ' .nav-by-division-body').removeClass('active show');

                        $(selector + ' .nav-by-division-header.' + refDivision).addClass('active').attr('aria-selected', 'true');
                        $(selector + ' .nav-by-division-body.' + refDivision).addClass('active show');
                    }
                }
            });

            let headerHeight = 0;

            let offsetTop = $(selector + ' .nav-by-division-header.active').offset().top - (headerHeight + 40);

            let $fisrtActiveX = $(selector + ' .nav-by-division-body.active .nav-by-scheduling-header.activeX:first');
            if ($fisrtActiveX.length) {
                offsetTop = $fisrtActiveX.offset().top - (headerHeight + 10);
            }

            $('html, body').animate({scrollTop: offsetTop}, 300, function () {
                animating = false;
            });
        }
    }, 1000));
});
