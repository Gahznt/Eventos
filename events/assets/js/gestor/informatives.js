$(function() {
  const resetInformativeForm = function () {
    $('#informativeForm').trigger('reset').find('select').change();
    $('#informative-id').val('');
    $('#informativeForm .card-header').removeClass('valid');
    tinymce.triggerSave();
    $('#delInformative').data('id', '').addClass('d-none');
  }

  $('#newInformative').on('click', function() {
    resetInformativeForm();
    $('#nav-1-form').click();
  });

  $('.editInformative').on('click', function() {
    const i = $(this).data('informative');
    $('input[name="informative-id"]').val(i.id);
    $('input[name="informative-date_enter"]').val(i.date_enter);
    $('input[name="informative-date_out"]').val(i.date_out);
    $(`select[name="informative-type"] option[value="${i.type}"]`).prop('selected', true).parent().change();
    $('input[name="informative-title-pt"]').val(i.title).change();
    $(`input[name="informative-status"][value=${i.status}]`).prop('checked', true);
    $('input[name="informative-highlight"]').prop('checked', i.highlight);
    tinymce.get('informative-desc-pt').setContent(i.desc);
    tinymce.triggerSave();
    $('#delInformative').data({
      'id': i.id,
      'name': i.title
    }).removeClass('d-none');
    $('#nav-1-form').click();
  });

  const resetTypeForm = function () {
    $('#typeForm').trigger('reset').find('select').change();
    $('#type-id').val('');
    $('#delType').data('id', '').addClass('d-none');
  }

  $('#newType').on('click', function() {
    resetTypeForm();
    $('#nav-2-form').click();
  });

  $('.editType').on('click', function() {
    const i = $(this).closest('tr').data('data');
    $('input[name="type-id"]').val(i.id);
    $(`select[name="type-order"] option[value="${i.order}"]`).prop('selected', true).parent().change();
    $('input[name="type-title-pt"]').val(i.title).change();
    $(`input[name="type-status"][value=${i.status}]`).prop('checked', true);
    $('input[name="type-hom"]').prop('checked', i.hom);
    $('#delInformative').data({
      'id': i.id,
      'name': i.title
    }).removeClass('d-none');
    $('#nav-2-form').click();
  });
});