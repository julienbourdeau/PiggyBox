(function ($) {

	$('a#remove-order-detail').on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

		if ($(this).parent().parent().parent().children().size() == 1) {
			$(this).parent().parent().parent().parent().parent().parent().parent().fadeOut(300, function() { $(this).remove(); refreshTotalPrice(); });
		}
		else{
        	$(this).parent().parent().fadeOut(300, function() { $(this).remove(); refreshTotalPrice(); });
		}	
    });

	$('div#quantity-minus.btn').click(function () { 
		var quantity = parseInt($(this).next().val());	
		if(quantity > 1){
			quantity--;
		}
		$(this).next().val(quantity);

		var unitPrice = $(this).parent().parent().parent().next().children().text(); 
		$(this).parent().parent().parent().next().next().next().children().text((parseFloat(unitPrice)*parseFloat(quantity)).toFixed(2));
		refreshTotalPrice();
    });

	$('div#quantity-plus.btn').click(function () { 
		var quantity = parseInt($(this).prev().val());	
		quantity++;
		$(this).prev().val(quantity);

		var unitPrice = $(this).parent().parent().parent().next().children().text(); 
		$(this).parent().parent().parent().next().next().next().children().text((parseFloat(unitPrice)*parseFloat(quantity)).toFixed(2));
		refreshTotalPrice();
    });

	function refreshTotalPrice(){
		var result = 0;

		$('td.col5').each(function(){
			result += parseFloat($(this).children().text());
			}
		);

		$('td.total-price').children().children().text(result);
	}


})(jQuery);
