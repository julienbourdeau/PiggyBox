
$('a[rel=tooltip]').tooltip();

$('.carousel').carousel({
  interval: 3000
});

jQuery("select.menu").change(function() {
  window.location = jQuery(this).find("option:selected").val();
});
