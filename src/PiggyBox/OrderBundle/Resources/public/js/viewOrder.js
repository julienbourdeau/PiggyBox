(function ($) {

	$('a#remove-order-detail').on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        $(this).parent().parent().remove();
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
