require('dependent-dropdown');

$(document).ready(function () {
  $('#search_theme').depdrop({
    //initDepends: ['thesis_division'],
    //initialize: true,
    depends: ['search_division'],
    url: $('#search_theme').data('route'),
    placeholder: $('#search_theme').data('select'),
    loading: false
  });
});
