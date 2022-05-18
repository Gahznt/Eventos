// $(document).ready(function () {
$(document).on('g-collection-update', function (e) {
    let $collection = $(e.target);
    let $fieldsets = $collection.find('.g-collection-fieldsets');
    let $fieldset = $fieldsets.find('.g-collection-fieldset');

    $collection.removeClass('g-collection-limit-min');
    $collection.removeClass('g-collection-limit-max');

    let min = parseInt($collection.data('min'));
    let max = parseInt($collection.data('max'));
    // let count = parseInt($collection.data('count'));
    let count = $fieldset.length;
    // let index = parseInt($collection.data('index'));

    if (count <= min) {
        $collection.addClass('g-collection-limit-min');
    }

    if (count >= max) {
        $collection.addClass('g-collection-limit-max');
    }

    $collection.data('count', count);
});

$(document).on('click', '.g-collection-add', function (e) {
    e.preventDefault();
    let $self = $(this);
    let $collection = $self.closest('.g-collection');

    // let min = parseInt($collection.data('min'));
    let max = parseInt($collection.data('max'));
    let count = parseInt($collection.data('count'));
    let index = parseInt($collection.data('index'));

    if (count >= max) {
        return;
    }

    let $fieldsets = $collection.find('.g-collection-fieldsets');

    let template = $collection.data('prototype');

    let $fieldset = $(template.replace(/__name__/g, index)).hide();

    $fieldsets.append($fieldset);

    $collection.data('index', index + 1);

    let name = $collection.data('name');
    if (name) {
        $collection.trigger('g-collection-add-' + name);
    }

    $collection.trigger('g-collection-update');

    $fieldset.slideDown(800);
});

$(document).on('click', '.g-collection-remove', function (e) {
    e.preventDefault();
    let $self = $(this);
    let $collection = $self.closest('.g-collection');

    let min = parseInt($collection.data('min'));
    // let max = parseInt($collection.data('max'));
    let count = parseInt($collection.data('count'));
    // let index = parseInt($collection.data('index'));

    if (count <= min) {
        // return;
    }

    let $fieldset = $self.closest('.g-collection-fieldset');

    $fieldset.slideUp(800, function () {
        let $_fieldset = $(this);
        let $_collection = $_fieldset.closest('.g-collection');

        $_fieldset.remove();

        let name = $_collection.data('name');
        if (name) {
            $_collection.trigger('g-collection-remove-' + name);
        }

        $_collection.trigger('g-collection-update');
    });
});

$(document).on('click', '.g-step-back', function (e) {
    e.preventDefault();

    let $self = $(this);

    let $handler = $('input[name="step"]');

    if (0 === $handler.length) {
        $handler = $('#step');
    }

    if (0 === $handler.length) {
        return;
    }

    let $steps = $('.step[data-step]');

    if (0 === $steps.length) {
        return;
    }

    let min = 999;
    let max = -999;

    $steps.each(function () {
        let $_step = $(this);
        let _step = parseInt($_step.data('step'));

        if (_step < min) {
            min = _step;
        }

        if (_step > max) {
            max = _step;
        }
    });

    if (999 === min && -999 === max) {
        return;
    }

    let $panels = $('.stepsTab[data-step]');
    $panels.hide();

    $self.addClass('disabled').hide();

    let step = parseInt($handler.val());

    $steps.filter('[data-step="' + step + '"]').removeClass('active');
    $panels.hide();

    if (step > min) {
        step--;
    }

    $steps.filter('[data-step="' + step + '"]').removeClass('complete').addClass('active');
    $panels.filter('[data-step="' + step + '"]').show();
    $handler.val(step);

    if (step > min && step < max) {
        $self.removeClass('disabled').show();
    }

    $('.mobileSteps span').text(step);
});

$(document).on('click', '.g-step-forward', function (e) {
    $self = $(this);
    $self.find('.g-step-forward-loader').show();

    setTimeout(function () {
        $self.prop('disabled', true).addClass('disabled');
    }, 10);
});

$(document).on('keyup change', '[data-max-number-words]', function (e) {
    const $self = $(this);
    const maxNumberWords = $self.data('max-number-words');
    const minNumberWords = $self.data('min-number-words');
    const $missingWordsCounter = $($self.data('missing-words-counter'));
    const $remainingWordsCounter = $($self.data('remaining-words-counter'));

    const initialValue = $self.val();

    let splittedValue = initialValue
            ?.replace(/(\r)?\n/g, " $1\n")
            ?.replace(/[ ]+/g, ' ')
            ?.split(' ')
        || [];

    if (maxNumberWords && splittedValue.length > 0) {
        splittedValue = splittedValue.slice(0, maxNumberWords);
    }

    const currentNumberWords = initialValue.length
        ? splittedValue.length
        : 0;

    $self.val(splittedValue.join(' ')?.replace(/ (\r)?\n/g, "$1\n"));

    let diff;

    if ($missingWordsCounter.length) {
        diff = minNumberWords - currentNumberWords;
        $missingWordsCounter.text(diff > 0 ? diff : 0);
    }
    if ($remainingWordsCounter.length) {
        diff = maxNumberWords - currentNumberWords;
        $remainingWordsCounter.text(diff > 0 ? diff : 0);
    }
});

$('[data-max-number-words]').each(function () {
    const $self = $(this);
    $self.trigger('keyup');
});

// });
