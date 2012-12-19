
$('a[rel=tooltip]').tooltip();

$('.carousel').carousel({
  interval: 3000
});

$('input[type="radio"]').change(function() {
   
   $('input[type="radio"]').each(function() {
	   if($(this).is(':checked'))  {
	  		var theID = $(this).attr('id');
	  		$('label[for="'+ theID +'"]').children('.product-item').addClass('selectedItem');
	    } else {
	  		var theID = $(this).attr('id');
	  		$('label[for="'+ theID +'"]').children('.product-item').removeClass('selectedItem');
	    }
   });

});