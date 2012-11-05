(function ($) {
	function refreshOrders(){
		var target = $('div.to_validate_ajax');
		console.log("interval called");
		if (target.attr('data-load-more')) {
			console.log("attr");
			$.ajax({
				url: target.attr('data-load-more'),
				dataType: 'json',
				ifModified: true,
				success: function (data) {
					//var notify = humane.create({ timeout: 4000, baseCls: 'humane-bigbox' });
					//humane.log("Options can be passed", { timeout: 1000, baseCls: 'humane-bigbox', waitForMove: true });
					var test = $('sup#super-badge.badge.badge-sucess').val();
					console.log(test);
					target.html(data.content);
				},
			});
		}
	}
	
	var refreshInterval = setInterval(refreshOrders, 1000*60);
	
})(jQuery);
