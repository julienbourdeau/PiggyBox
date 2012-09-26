(function ($) {
    $('.details-toggler').click(function (e) {
        var target = $(this);
        target.toggleClass('hide')
            .prev().toggleClass('open');

        if (target.attr('data-load-more')) {
            $.ajax({
                url: target.attr('data-load-more'),
                dataType: 'json',
                success: function (data) {
                    target.attr('data-load-more', '')
                        .prev().html(data.content);
                }
            });
        }
    });
		//     $('.category-item').click(function (e) {
		//         var target = $(this);
		// 
		// if(!target.parent().hasClass('active')){
		// 	target.parent().toggleClass('active');
		// 	$('.category-item').not(target).parent().removeClass('active');
		// 
		// 	$(".product-item").hide();
		// 	target.removeClass('category-item');
		// 	$(".product-item."+target.attr('class')).fadeIn("slow", "linear");
		// 	$(target.attr('class')).toggleClass('hide');	
		// }
		//     });
	
})(jQuery);
