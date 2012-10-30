
$('a[rel=tooltip]').tooltip();

$('.carousel').carousel({
  interval: 3000
});


jQuery("ul.nav li select").change(function() {
  window.location = jQuery(this).find("option:selected").val();
});
