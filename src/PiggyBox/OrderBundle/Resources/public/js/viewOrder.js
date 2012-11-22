(function ($) {

	$('a#remove-order-detail').on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

		if ($(this).parent().parent().parent().children().size() == 1) {
			$(this).parent().parent().parent().parent().parent().parent().parent().fadeOut(300, function() { $(this).remove(); });
		}
		else{
        	$(this).parent().parent().fadeOut(300, function() { $(this).remove(); });
		}	
    });

	$('div#quantity-minus.btn').click(function () { 
		var quantity = parseInt($(this).next().val());	
		if(quantity > 1){
			quantity--;
		}
		$(this).next().val(quantity);
    });

	$('div#quantity-plus.btn').click(function () { 
		var quantity = parseInt($(this).prev().val());	
		quantity++;
		$(this).prev().val(quantity);
    });
})(jQuery);
