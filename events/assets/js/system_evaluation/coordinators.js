require('dependent-dropdown');

$(function() {
  $('#checkAll').on('change', function() {
    $('input[name="del[]"]').prop('checked', $(this).is(':checked'));
  });

  $('select[name="coordinators_search[userThemes]"]').depdrop({
    //initDepends: ['coordinators_search_division'],
    //initialize: true,
    depends: ['coordinators_search_division'],
    url: $('#coordinators_search_userThemes').attr('route'),
    placeholder: $('#coordinators_search_userThemes').attr('select'),
    loading: false
  });
});
