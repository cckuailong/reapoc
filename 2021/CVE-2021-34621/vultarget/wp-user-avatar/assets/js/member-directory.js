(function ($) {

    $(function () {

        $('.ppressmd-member-directory-sorting-a-text').on('click', function (e) {

            e.preventDefault();

            $(this).blur().next().toggle()
        });

        // close dropdown menu when outside is clicked.
        $(document).on('click', function (e) {
            if ($(e.target).prop('class') !== 'ppressmd-member-directory-sorting-a-text') {
                $('.ppressmd-member-directory-sorting-a .ppressmd-new-dropdown').hide();
            }
        });

        var filterStateVar = null;

        $('.ppressmd-member-directory-filters-a').on('click', function (e) {

            var _this = $(this), parent = _this.parents('.ppressmd-member-directory-header');

            filterStateVar = filterStateVar === null ? parent.hasClass('ppmd-filters-expand') : filterStateVar;

            e.preventDefault();

            filterStateVar = !filterStateVar;

            $('a', _this).blur();

            if (filterStateVar) {
                $('.ppressmd-member-directory-filters-bar', parent).removeClass('.ppressmd-header-row-invisible')
                    .find('.ppressmd-search').css('display', 'grid');
                $('.ppress-material-icons.ppress-down', _this).hide();
                $('.ppress-material-icons.ppress-up', _this).css('display', 'inline');

            } else {
                $('.ppressmd-member-directory-filters-bar', parent).addClass('.ppressmd-header-row-invisible')
                    .find('.ppressmd-search').css('display', 'none');
                $('.ppress-material-icons.ppress-down', _this).css('display', 'inline');
                $('.ppress-material-icons.ppress-up', _this).hide();
            }
        });

        $('.ppmd-select2').select2();

        $('.ppmd-date').each(function () {
            $(this).flatpickr($(this).data('config'));
        })
    });

    $(window).on('load', function () {

        var $grid = $('.ppmd-members-wrap').imagesLoaded(function () {
            $grid.masonry({
                itemSelector: '.ppmd-member-wrap',
                columnWidth: '.ppmd-member-wrap',
                gutter: '.ppmd-member-gutter',
                percentPosition: true
            });
        });
    });

})(jQuery);