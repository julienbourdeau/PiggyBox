(function ($) {

	$('a#remove-order-detail').on('click', function(e) {
        e.preventDefault();
		var selector = $(this).attr('class')
		var shopSlug = $(this).attr('rel')

		if ($('tbody#'+shopSlug).children().size() == 1) {
			$('div#'+shopSlug+'.container').fadeOut(300, function() { $(this).remove(); });
		}
		else{
        	$('tr#'+selector).fadeOut(300, function() { $(this).remove(); refreshTotalPrice(shopSlug); });
		}	
    });

	$('div#quantity-minus.btn').click(function () { 
		var quantity = parseInt($(this).next().val());	
		if(quantity > 1){
			quantity--;
		}
		$(this).next().val(quantity);

		var selector = $(this).attr('rel');
		var unitPrice = $('td#'+selector+'.col3').text();
		$('td#'+selector+'.col5').text((quantity*parseFloat(unitPrice)).toFixed(2));
		refreshTotalPrice($(this).attr('data-shop'));
    });

	$('div#quantity-plus.btn').click(function () { 
		var quantity = parseInt($(this).prev().val());	
		quantity++;
		$(this).prev().val(quantity);

		var selector = $(this).attr('rel');
		var unitPrice = $('td#'+selector+'.col3').text();
		$('td#'+selector+'.col5').text((quantity*parseFloat(unitPrice)).toFixed(2));
		refreshTotalPrice($(this).attr('data-shop'));
    });

	function refreshTotalPrice(selector){
		var result = 0;

		$('table#'+selector+' td.col5').each(function(){
			result += parseFloat($(this).text());
			}
		);

		result = result.toFixed(2);
		$('table#'+selector+'-total td.total-price strong span').text(result);
	}
})(jQuery);
