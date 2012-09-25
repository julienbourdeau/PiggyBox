(function ($) {
    $('.details-toggler').click(function (e) {
        var target = $(this);
        target.toggleClass('open')
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
	
})(jQuery);
