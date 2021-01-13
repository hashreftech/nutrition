/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    "jquery/ui",
    "magebees/fpowlcarousel"
], function ($,ui) {
    "use strict";

    $.widget('magebees.magebeesFeatured', {
        
        options: {
            nav:'',
            autoplay:''
        },
        is_loading: 0,
        _create: function (options) {
            this._initialize();
        },
        _initialize: function () {
            var self = this;
            self.removeRedirectAttr();
            self.findPaginationElement();
            self.addSlider();
                $(document).on('DOMNodeInserted','.cwsFeatured', function (e) {
                    
                        self.removeRedirectAttr();
                        self.findPaginationElement();
                });
        },
        removeRedirectAttr: function () {
            var self = this;
          $("body .mageFeaturedToolbar").find('[ data-mage-init]')
            .each(function (index, element) {
                var ele_id=$(element).attr('id');
                if (ele_id=="limiter") {
                $(element).removeAttr('data-mage-init');
                     $(element).change(function () {
                         var url=$(this).val();
                         self.ajaxLoadContent(url);
                     });
                }
            });
        },
        findPaginationElement: function () {
            var self = this;
            $("body .mageFeaturedToolbar").find("a").each(function () {
                var link = $(this);
                var link_class= $(this).attr("class");
                var classes = [ "page","action  previous","action  next"];
                var found_class = $.inArray(link_class,classes);
                if (found_class >-1) {
                link.attr("onclick", "return false;");
                    var url = link.attr("href");
                    link.click(function () {
                        
                         if (self.is_loading == 0) {
                        self.ajaxLoadContent(url);
                         }
                    });
                }
            });
        },
        ajaxLoadContent: function (url) {
            var self = this;
            self.is_loading = 1;
                $.ajax({
                        url: url,
                        type: "GET",
                        cache:false,
                        beforeSend : function () {
                               
                            $('body').addClass('fp-stop-scrolling');
                            $('#fp_scroll_loading').css('display','block');
                        },
                        success: function (data) {
                            self.is_loading = 0;
                            $('body').removeClass('fp-stop-scrolling');
                            history.pushState({}, "", url);
                            $('#fp_scroll_loading').css('display','none');
                            var page_content=$(data).find('.cwsFeatured').html();
                            if (page_content==undefined) {
                            var page_content=$(data).filter('.cwsFeatured').html();
                            }
                            $(".cwsFeatured").html(page_content);
                            $(".cwsFeatured").trigger('contentUpdated');
                        }
                        });
        },
        addSlider: function () {
            var nav=this.options.nav;
            var navigation = nav === "true";
            var autoplay=this.options.autoplay;
            var autoplayslider = autoplay === "true";
            var owl = $('#'+this.options.slider_id);
			var item_amount = parseInt(this.options.product_count);
			
			var enable_loop5 = 0;
			var enable_loop1 = 0;
			var enable_loop2 = 0;
			var enable_loop3 = 0;
			var enable_loop4 = 0;				
			
            if ( item_amount <=1 ) {
            enable_loop1 = false;
            }else {
            enable_loop1 = true;
            }
            if ( item_amount <=2 ) {
            enable_loop2 = false;
            }else {
            enable_loop2 = true;
            }
			if ( item_amount <=3 ) {
            enable_loop3 = false;
            }else {
            enable_loop3 = true;
            }
			if ( item_amount <=4 ) {
            enable_loop4 = false;
            }else {
            enable_loop4 = true;
            }
			if ( item_amount <=5 ) {
            enable_loop5 = false;
            }else {
            enable_loop5 = true;
            }			
         
            owl.owlCarousel({
                slideSpeed:200,
                paginationSpeed: 800,
                nav:navigation,
                dots:false,
                autoplay:autoplayslider,
                margin:20,
              //  loop:true,
                responsiveClass:true,
                responsive:{
                    300:{ items:1,loop:enable_loop1 },
                    479:{ items:2,loop:enable_loop2 },
                    600:{ items:2,loop:enable_loop2 },
                    767:{ items:3,loop:enable_loop3 },
                    999:{ items:4,loop:enable_loop4 },
                    1280:{ items:5,loop:enable_loop5 }
                }

            });
            if (autoplay === "true") {
            owl.on('mouseenter',function () {
               owl.trigger('stop.owl.autoplay');
              })
              owl.on('mouseleave',function () {
                   owl.trigger('play.owl.autoplay',[200]);
              });
            }
        }
    });

    return $.magebees.magebeesFeatured;
});
