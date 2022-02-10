jQuery(function($) {

    var tabs = $('#wpuf_subs_metabox nav li' ),
        items = $('#wpuf_subs_metabox .subscription-nav-content > section');

    tabs.first().addClass('tab-current');
    items.first().addClass('content-current');

    tabs.on('click', 'a', function(event) {
        event.preventDefault();

        var self = $(this);

        tabs.removeClass('tab-current');
        self.parent('li').addClass('tab-current');

        $.each(items, function(index, val) {
            var element = $(val);

            if ( '#' + element.attr( 'id' ) === self.attr('href') ) {
                element.addClass('content-current');
            } else {
                element.removeClass('content-current');
            }
        });
    });
});
