(function ($) {
	function refreshOrders(){
		var target = $('div.to_validate_ajax');
		console.log("interval called");
		if (target.attr('data-load-more')) {
			console.log("attr");
			$.ajax({
				url: target.attr('data-load-more'),
				dataType: 'json',
				success: function (data) {
					target.html(data.content);
				},
				error: function(xhr,err){
				}
			});
		}
	}
	
	var refreshInterval = setInterval(refreshOrders, 1000*10);
	
})(jQuery);
