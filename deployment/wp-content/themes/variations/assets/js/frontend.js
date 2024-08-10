; (function ($) {
    $(function () {

        /**
         * Toggle Sub Menu
         */
        window.variationsMenuManager = window.variationsMenuManager || {

            subMenuOpenButton: '.wp-block-navigation-submenu__toggle',
            subMenuElements: '.wp-block-navigation-item.has-child > a',
            menuLinks: '.wp-block-navigation a',
            closeButton: '.wp-block-navigation__responsive-container-close',

            checkMenuLinks: function () {

                const _this = this;

                $(this.menuLinks).on('click', function () {

                    const href = $(this).attr('href');
                    const regex = new RegExp('#');

                    if (regex.test(href)) {

                        $(_this.closeButton).trigger('click');
                    }
                });
            },

            bindToggleSubMenu: function () {

                // click on <a>
                $(this.subMenuElements).on('click', function (e) {

                    if ($(window).width() <= 767) {

                        e.preventDefault();

                        // check if there is an open button
                        if ($(this).parent().find('button').length === 0) {

                            $(this).parent().children('ul').slideToggle('fast');
                        } else {

                            window.location.href = $(this).attr('href');
                        }
                    }
                });

                // click on <button>
                $(this.subMenuOpenButton).on('click', function (e) {

                    if ($(window).width() <= 767) {

                        e.preventDefault();

                        $(this).parent().children('ul').slideToggle('fast');
                    }
                });
            },

            init: function () {

                // Toggle SubMenu
                this.bindToggleSubMenu();

                // Close Menu on mobile if there is a #
                this.checkMenuLinks();
            }
        }

        variationsMenuManager.init();

        /**
         * Sticky Header
         */
        window.variationsManageStickyHeader = window.variationsManageStickyHeader || {

            header: '.is-position-sticky',
            cleanClass: 'mx_clean_header_background',

            manageHeader: function () {

                if ($(this.header).length === 0) return;

                const window_width = $(window).width();

                if (window_width < 768) {

                    $(this.header).removeClass(this.cleanClass);
                    
                    return;
                }

                const scroll_position = $(document).scrollTop();

                if (scroll_position < 50) {

                    $(this.header).addClass(this.cleanClass);
                } else {

                    $(this.header).removeClass(this.cleanClass);
                }
            },

            init: function () {

                const _this = this;

                // Check when page is ready
                this.manageHeader();

                // Check on scroll event
                $(window).on('scroll', function () {

                    _this.manageHeader();
                });
            }
        }

        variationsManageStickyHeader.init();
    });
})(jQuery);