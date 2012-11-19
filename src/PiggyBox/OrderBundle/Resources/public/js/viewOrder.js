(function ($) {
	$('form#checkout').submit(function(e) {

		var url = $(this).attr("action");

		$.ajax({
			type: "POST",
			url: url, // Or your url generator like Routing.generate('discussion_create')
			data: $(this).serialize(),
			dataType: "json",
			success: function(data){
				$('div.modal-body.row-fluid').html(data.content);
				$('#myModal').modal();
			}
		});
		return false;
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
