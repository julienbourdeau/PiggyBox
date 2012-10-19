(function ($) {
    $('.details-toggler').click(function (e) {
        var target = $(this);
        target.toggleClass('hide')
            .prev().toggleClass('open');

        if (target.attr('data-load-more')) {
            $.ajax({
                url: target.attr('data-load-more'),
                dataType: 'json',
				ifModified: true,
                success: function (data) {
                    target.attr('data-load-more', '')
                        .prev().html(data.content);
                }
            });
        }
    });
})(jQuery);
