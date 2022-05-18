$(function() {
  $('#checkAll').on('change', function() {
    $('input[name="del[]"]').prop('checked', $(this).is(':checked'));
  });

  $('.admCheck').each(function() {
    const geral = $(this).find('input[name="adm-geral[]"]');
    const op = $(this).find('input[name="adm-op[]"]');
    geral.on('change', function() {
      if($(this).is(':checked')) {
        op.prop('checked', false);
      }
    });
    op.on('change', function() {
      if($(this).is(':checked')) {
        geral.prop('checked', false);
      }
    });
  });

  $('#action').on('change', function() {
    if($(this).val() == 0) {
      $('input[name="adm-geral[]"]').prop('checked', true);
      $('input[name="adm-op[]"]').prop('checked', false);
    } else if($(this).val() == 1) {
      $('input[name="adm-geral[]"]').prop('checked', false);
      $('input[name="adm-op[]"]').prop('checked', true);
    }
  });
});