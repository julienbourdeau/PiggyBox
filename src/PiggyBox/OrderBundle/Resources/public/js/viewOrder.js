(function ($) {

	$(document).ready(function () {
		$("td.col5").each(function () {
			var selector = $(this).attr('id');
			var quantity = parseInt($('input.'+selector).val());
			var unitPrice = parseFloat($('td#'+selector+'.col3 > span').text()).toFixed(2);

			if ($(this).attr('rel') != 'formule' ) {
				$('td#'+selector+'.col5 > input').val(parseFloat(quantity*unitPrice).toFixed(2));
				$('td#'+selector+'.col5 > span').text(parseFloat(quantity*unitPrice).toFixed(2));
			}
			if ($(this).attr('rel') == 'formule' ) {
				$('td#'+selector+'.col5 > input').val(parseFloat($('td#'+selector+'.col5 > span').text()).toFixed(2));
			}
		});

		$("table").each(function () {
			var selector = $(this).attr('id');
			refreshTotalPrice(selector);
		});
	});

	$('select').on('change', function() {
		var selector = $(this).attr('rel')
	
		var quantity = parseInt($(this).val());	
		var min_weight = parseFloat($(this).attr('data-min-weight')).toFixed(2);	
		var max_weight = parseFloat($(this).attr('data-max-weight')).toFixed(2);	
		var min_person = parseInt($(this).attr('data-min-person'));
		var max_person = parseInt($(this).attr('data-max-person'));
		var weight_price = parseFloat($('td#'+selector+'.col4').text()).toFixed(2);

		var result = (parseFloat(parseFloat((((max_weight-min_weight)/(max_person-min_person))*(quantity-min_person)))+parseFloat(min_weight))*parseFloat(weight_price)/1000).toFixed(2); 

		$('td#'+selector+'.col3 > span').text(result);
		$('td#'+selector+'.col5 > span').text(result);
		$('td#'+selector+'.col5 > input').val(result);
		refreshTotalPrice($(this).attr('data-shop'));
    });

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
		$('td#'+selector+'.col5 > span').text((quantity*parseFloat(unitPrice)).toFixed(2));
		$('td#'+selector+'.col5 > input').val((quantity*parseFloat(unitPrice)).toFixed(2));
		refreshTotalPrice($(this).attr('data-shop'));
    });

	$('div#quantity-plus.btn').click(function () { 
		var quantity = parseInt($(this).prev().val());	
		quantity++;
		$(this).prev().val(quantity);

		var selector = $(this).attr('rel');
		var unitPrice = $('td#'+selector+'.col3').text();
		$('td#'+selector+'.col5 > span').text((quantity*parseFloat(unitPrice)).toFixed(2));
		$('td#'+selector+'.col5 > input').val((quantity*parseFloat(unitPrice)).toFixed(2));
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
		$('table#'+selector+'-total td.total-price > input').val(result);
	}
})(jQuery);
