$(function() {
  // temporariamente remove o breadcumb que está fixo no base.html.twig
  $('.content').removeClass('hasBreadcumb');
  $('#breadcumb').parent().remove();
});
