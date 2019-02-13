function elementsAnimate() {
    var windowWidth = window.innerWidth || document.documentElement.clientWidth;
    var animate = jQuery('.animate');
    var animateDelay = jQuery('.animate-delay-outer');
    var animateDelayItem = jQuery('.animate-delay');
    if (windowWidth > 767 && !isiPhone()) {
        animate.bind('inview', function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) {
                jQuery(this).addClass("animated");
            }
        });
        animateDelay.bind('inview', function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) {
                var j = -1;
                var $this = jQuery(this).find(".animate-delay");
                $this.each(function () {
                    var $this = jQuery(this);
                    j++;
                    setTimeout(function () {
                        $this.addClass("animated");
                    }, 200 * j);
                });
                jQuery(this).addClass("animated");
            }spy
        });
    } else {
        animate.each(function () {
            jQuery(this).removeClass('animate');
        });
        animateDelayItem.each(function () {
            jQuery(this).removeClass('animate-delay');
        });
    }
}
if (jQuery.browser.version > 8.0)
{
$jq=jQuery.noConflict();

 // Check setLocation is add product to cart
	// issend Variable check request on send
	var toolbarsend	=	false;
	var toolbarBaseurl	='';
	var ajaxtoolbar	=	function(){
		function lockshowloading(){

            jQuery('#preloader .loader').show();
		}
		return {
			onReady:function(){
				setLocation=function(link){

					if(link.search("limit=")!=-1||link.search("price=")!=-1||link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("order=")!=-1){
						if(toolbarsend==false){
							ajaxtoolbar.onSend(link,'get');
						
						}
					}else{
                        window.location.href=link;
                    }
                    
				};

				$jq('a','.toolbar').click(function(event) {
					link	=	$jq(this).attr('href');

                    if((link.search("mode=")!=-1||link.search("dir=")!=-1||link.search("price=")!=-1||link.search("p=")!=-1)&&(toolbarsend==false)){
						event.preventDefault();
						ajaxtoolbar.onSend(link,'get');
					}

                    return false;
					
				});
				
			},//End onReady
			onSend:function(url,typemethod){
				new Ajax.Request(url,
					{parameters:{ajaxtoolbar:1},
					method:typemethod,
					onLoading:function(cp){
						toolbarsend=true;
						lockshowloading();
					},
					onComplete:function(cp){
						toolbarsend=false;
						if(200!=cp.status){
							return false;
						}else{
							// Get success	
							var list	=	cp.responseJSON;
							$$(".category-products").invoke("replace",list.toolbarlistproduct);
							ajaxtoolbar.onReady();
                            jQuery("select.custom").selectbox();
                            retinaProducts();
                            jQuery('#preloader .loader').hide();
                            previewInit();

                            jQuery('.fancybox').fancybox(
                                {
                                    hideOnContentClick : true,
                                    width: 876,
                                    height:450,
                                    margin:0,
                                    padding:0,
                                    autoDimensions: true,
                                    type : 'iframe',
                                    showTitle: false,
                                    scrolling: 'no',

                                    onComplete: function(){
                                        jQuery('#fancybox-frame').load(function () {
                                            jQuery('#fancybox-content').height(jQuery(this).contents().height());
                                            jQuery.fancybox.resize();

                                        });
                                    }
                                }
                            );
                            elementsAnimate();

						}
					}
				});
			}//End onSend	
		}
	}();
Prototype.Browser.IE?Event.observe(window,"load",function(){ajaxtoolbar.onReady()}):document.observe("dom:loaded",function(){ajaxtoolbar.onReady()});
}
