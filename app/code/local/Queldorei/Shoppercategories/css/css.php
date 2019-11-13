<?php
$config = $data;
$color_helper = Mage::helper('shoppercategories/color');
?>
/*Category Styles*/
<?php if ( $config['enable_font'] == 1 ) : ?>
.checkout-onepage-success .sub-title,
.contacts-index-index .fieldset h2.legend,
.std h1, .std h2, .std h3, .std h4,
.page-title h1, .page-title h2,
.cart-top span,
.cart-top-title,
.compare-top-title,
.search-top-container .search-form .search-top-title,
.footer-info h4,
.price-box .price,
nav .nav-top-title, .nav-container .nav-top-title,
#queldoreiNav>li>a,
#queldoreiNav li.custom-block div.sub-wrapper strong,
#nav>li>a, #mobile-nav>li>a,
#nav li.custom-block ul li strong,
.wide-navigation #nav ul.level0 > li > a,
.homepage-banners a .content strong,
.slider-container h3,
.slider-container .jcarousel-list h3,
.category-description h1,
.category-description strong,
.products-grid h3,
.cart .cart-collaterals h2,
#shopping-cart-totals-table strong,
#product-customer-reviews .review-title,
.add-review h3.title,
#customer-reviews .form-add h2,
.top-opc li .number,
.opc .step-title,
.opc h3,
.block .block-title strong,
.cms-index-index .block .block-title strong,
.block-poll .question,
.block-layered-nav dt,
.product-tabs-accordion h2.tab-heading a,
.product-category-title,
.page-head h3,
.product-view h1,
.product-view h2,
.product-view .box-tags h3,.product-view .box-tags .form-add label,
.product-view .product-additional .block .block-title strong,
.box-up-sell2 h3,
.box-up-sell2 .jcarousel-list h3,
.flexslider .slides .content strong,
.product-slider-container h2,
.block-slider .slides > li strong.block-slider-title                            { font-family:"<?php echo $config['font']; ?>"}
<?php endif; ?>
<?php if ( !empty($config['color']) ) : ?>
/**~~ Theme Color ~~**/
button.button span,
.quick-view,
button.btn-cart.ajax-cart span,
.qty-elements a, div.summary-collapse:before,
.flexslider:hover a:hover,
.btn-remove, .btn-edit,
.cart-top > a,
.cart-top-container .details-border,
.cart-top-title a span.icon,
.compare-top,
.compare-top-container .details-border,
.compare-top-title a span.icon,
.search-top,
.search-form-border,
.search-top-container .search-form .search-top-title span.icon,
.footer-info ul.twitterList li span.tweet-icon,
.footer-info ul.social li a:hover,
.footer-info .block-control:hover,
.footer-info .block-control-hide:hover,
.products-grid .hover .price-box,
.products-grid .hover .button-container button.button span span em,
.products-list .button-container .btn-add-cart span,
.data-table .btn-remove2,
.data-table .btn-edit2,
.top-opc li.allow:hover .number,
.product-view .add-to-links li:hover span.icon,
.flex-control-paging li a:hover,
.flex-control-paging li a.flex-active,
#toTop,
.jcarousel-prev-horizontal:hover,
.jcarousel-next-horizontal:hover,
.product-view .box-up-sell .jcarousel-next-horizontal:hover,
.product-view .box-up-sell .jcarousel-prev-horizontal:hover,
nav .nav-top-title:hover, .nav-container .nav-top-title:hover,
nav .nav-top-title.active, .nav-container .nav-top-title.active,
nav .nav-top-title div.icon span, .nav-container .nav-top-title div.icon span,
.tags-list li a:hover,
.block-tags .block-content li a:hover,
.product-view .box-tags ul.product-tags li:hover                                { background-color:<?php echo $config['color']; ?>}

.tags-list li a:hover,
.block-tags .block-content li a:hover,
.product-view .box-tags ul.product-tags li:hover a                                { border-color:<?php echo $config['color']; ?>}

/*-- hover --*/
button.button:hover span,
.btn-remove:hover,
.btn-edit:hover,
.quick-view:hover,
.data-table .btn-edit:hover,
.data-table .btn-remove:hover,
.products-grid .hover .button-container button.button:hover span span em        { background-color:#393431;}

button.button_white span                                                        { background-color: #fff;}
button.button_white:hover span                                                  { background:<?php echo $config['color']; ?>}

/*-- invert styles --*/
button.invert span,
.buttons-set .back-link a,
.cart-shipping button.button span,
.cart-coupon button.button span,
.compare-top-container .details .actions a:before,
.block-compare .block-content .actions a:before                                 { background-color:#393431;}
/*-- invert styles theme hover --*/
button.invert:hover span,
.buttons-set .back-link a:hover,
.cart-shipping button.button:hover span,
.cart-coupon button.button:hover span,

.block .block-content a.shopby_more:hover,
.block .block-content a.shopby_more_hide,
.block .block-content a.shopby_more_hide:hover                                  { background-color:<?php echo $config['color']; ?>}


/***** iPad Smaller than 959px *****/
@media only screen and (max-width: 959px) {
    #queldoreiNav>li>a, #nav>li>a, #mobile-nav>li>a {border-radius:3px;}
    #queldoreiNav>li>a:hover, #nav>li>a:hover, #mobile-nav>li>a:hover,
    #queldoreiNav>li.over>a, #nav>li.over>a, #mobile-nav>li.over>a,
    #queldoreiNav>li.active>a, #nav>li.active>a, #mobile-nav>li.active>a        { background-color:<?php echo $config['color']; ?>; color:#fff; border-radius:3px;}
}
/***** iPad Smaller than 479px *****/
@media only screen and (max-width: 479px) {
    .nav-container .nav-top-title                                               { background-color:<?php echo $config['color']; ?>}
    .nav-container .nav-top-title div.icon span                                 { background-color: #fff;}
    .nav-container .nav-top-title:hover                                         { background-color: #322c29;}
}

.footer-info a,
.price-box .price,
.pager .pages li a:hover,
.pager .pages .current,
.cms-contacts-page .contact_block p a,
.toolbar-dropdown ul li a:hover, .toolbar-dropdown ul li.selected a,
.products-grid .add-to-links li span,
.opc h3, .opc h4,
.block-progress dt:before,
#checkout-step-login .block-checkout-register ul.ul li:before
{color:<?php echo $config['color']; ?>}

.footer-info .information ul li:before, .footer-info ul.disc li:before,
#checkout-step-login .block-checkout-register ul.ul li:before,
.block-progress dt:before                                                       { background-color:<?php echo $config['color']; ?>}

.cart-top-container .details-border:before                                      { border-color: transparent transparent <?php echo $config['color']; ?> transparent;}
.compare-top-container .details-border:before                                   { border-color: transparent transparent <?php echo $config['color']; ?> transparent;}
.search-form-border:before                                                      { border-color: transparent transparent <?php echo $config['color']; ?> transparent;}
#queldoreiNav>li>a:hover>span,
#queldoreiNav>li.over>a>span,
#queldoreiNav>li.active>a>span,
#nav>li>a:hover>span,
#nav>li.over>a>span,
#nav>li.active>a>span,
#mobile-nav>li>a:hover>span,
#mobile-nav>li.over>a>span,
#mobile-nav>li.active>a>span,
.cart .cart-collaterals .cart-block,
.opc .step,
.block,
.cms-index-index .block,
.block-login,
.cart .cart-collaterals .cart-block,
.cms-contacts-page .cform_wrap,
.product-tabs li.active,
.product-tabs-container h2.active, 
.ui-tabs .ui-tabs-nav li.ui-tabs-active,
#customer-reviews                                                               { border-top-color:<?php echo $config['color']; ?>;}

/** links hover color **/
.page-title a.link-reorder:hover,.page-title a.link-print:hover,
.box-account .box-head a:hover,
.dashboard .box-reviews li.item a:hover,
.dashboard .box .box-title a:hover,
.box-content address a:hover,
.addresses-list a:hover,
.header-container .links li a:hover,
.block .block-content a:hover, .block .block-content li a:hover, .block .block-content li.item a:hover,
.cms-index-index .block .block-content a:hover, .cms-index-index .block .block-content li a:hover, .cms-index-index .block .block-content li.item a:hover,
.block-layered-nav .block-content dd li a:hover,
.no-rating a:hover, .ratings a:hover,
.product-view .box-up-sell .product-name:hover,
.data-table td a:hover                                                          { color:<?php echo $config['color']; ?>}
<?php endif; ?>

<?php if ( !empty($config['title_color']) ) : ?>
h1, h2, h3,
.std h1, .std h2, .std h3, .std h4,
.page-title h1, .page-title h2,
.page-head-alt h3,
.block .block-title,
.cms-index-index .block .block-title,
.block-login .block-title,
.product-view .product-additional .block .block-title,
.footer-info h4,
#checkout-review-table h3,
.product-category-title,
.page-head h3,
.product-view h1, .product-view h2,
#shopping-cart-totals-table strong,
.product-slider-container h2                                                    { color:<?php echo $config['title_color']; ?>}
<?php endif; ?>

<?php if ( !empty($config['header_bg']) ) : ?>
.header-container, header.fixed                                                 { background-color:<?php echo $config['header_bg']; ?>}
<?php endif; ?>

<?php if ( !empty($config['menu_text_color']) ) : ?>
#queldoreiNav > li > a, #nav > li > a, #mobile-nav > li > a                     { color:<?php echo $config['menu_text_color']; ?>}
<?php endif; ?>

<?php if ( !empty($config['slideshow_bg']) ) : ?>
.slider {background-color:<?php echo $config['slideshow_bg']; ?>}
<?php endif; ?>

<?php if ( !empty($config['content_bg']) ) : ?>
body, .main-container, .footer-container .product-slider-container {background-color:<?php echo $config['content_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['content_bg_img']) && $config['content_bg_img_mode'] == 'tile' ) : ?>
.main-container                                                                 { background-image:url('<?php echo Mage::getBaseUrl('media') . 'queldorei/shopper/' . $config['content_bg_img']; ?>'); background-position:top left; background-repeat:repeat}
<?php endif; ?>
<?php if ( !empty($config['content_link']) ) : ?>
.block .block-content a, .block .block-content li a, .block .block-content li.item a,
.cms-index-index .block .block-content a, .cms-index-index .block .block-content li a, .cms-index-index .block .block-content li.item a,
.block-layered-nav .block-content dd li a,
.no-rating a, .ratings a,
.product-view .box-up-sell .product-name,
.data-table td a:hover, .data-table td .product-name a                          { color:<?php echo $config['content_link']; ?>}
.data-table td .product-name a                                                  { color:<?php echo $config['content_link']; ?>}

.block-tags .block-content li a,
.product-view .box-tags ul.product-tags li,
.product-view .box-tags ul.product-tags li a                                    { color:rgba(<?php echo $color_helper->hex2RGB($config['content_link'], 1); ?>, 0.76)}
<?php endif; ?>
<?php if ( !empty($config['content_link_hover']) ) : ?>
.page-title a.link-reorder:hover,.page-title a.link-print:hover,
.box-account .box-head a:hover,
.dashboard .box-reviews li.item a:hover,
.dashboard .box .box-title a:hover,
.box-content address a:hover,
.addresses-list a:hover,
.block .block-content a:hover, .block .block-content li a:hover, .block .block-content li.item a:hover,
.cms-index-index .block .block-content a:hover, .cms-index-index .block .block-content li a:hover, .cms-index-index .block .block-content li.item a:hover,
.block-layered-nav .block-content dd li a:hover,
.no-rating a:hover, .ratings a:hover,
.product-view .box-up-sell .product-name:hover,
.data-table td a, .data-table td .product-name a:hover                          { color:<?php echo $config['content_link_hover']; ?>}
.block-tags .block-content li a:hover,
.product-view .box-tags ul.product-tags li:hover,
.product-view .box-tags ul.product-tags li:hover a                              { color:#fff;}
<?php endif; ?>


<?php if ( !empty($config['page_title_bg']) ) : ?>
.page-title-bg,
.page-title:before, .breadcrumbs:before, .page-head h3:before, .product-category-title:before {background-color:<?php echo $config['page_title_bg']; ?>}
<?php endif; ?>

<?php if ( !empty($config['toolbar_bg']) ) : ?>
.top-switch-bg {background-color:<?php echo $config['toolbar_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['toolbar_color']) ) : ?>
.header-switch span.current {color:<?php echo $config['toolbar_color']; ?>}
.header-container .links li a, .header-switch span {color:rgba(<?php echo $color_helper->hex2RGB($config['toolbar_color'], 1); ?>, 0.65)}
<?php endif; ?>
<?php if ( !empty($config['toolbar_hover_color']) ) : ?>
.header-container .links li a:hover {color:<?php echo $config['toolbar_hover_color'] ?>}
<?php endif; ?>

<?php if ( !empty($config['footer_bg']) ) : ?>
.footer-container {background-color:<?php echo $config['footer_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_color']) ) : ?>
.footer-container, footer a, footer ul.links li a {color:<?php echo $config['footer_color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_hover_color']) ) : ?>
footer a:hover, footer ul.links li a:hover {color:<?php echo $config['footer_hover_color'] ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_banners_bg']) ) : ?>
.footer-banners {background-color:<?php echo $config['footer_banners_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_info_bg']) ) : ?>
.footer-info {background-color:<?php echo $config['footer_info_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_info_border']) ) : ?>
.footer-info {border-top-color:<?php echo $config['footer_info_border']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_info_title_color']) ) : ?>
.footer-info h4 {color:<?php echo $config['footer_info_title_color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_info_color']) ) : ?>
.footer-info, .footer-info ul.twitterList li {color:<?php echo $config['footer_info_color']; ?>}
.footer-info ul.twitterList li span.time-ago {color:rgba(<?php echo $color_helper->hex2RGB($config['footer_info_color'], 1); ?>, 0.85)}
<?php endif; ?>
<?php if ( !empty($config['footer_info_link_color']) ) : ?>
.footer-info a {color:<?php echo $config['footer_info_link_color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['footer_info_link_hover_color']) ) : ?>
.footer-info a:hover {color:<?php echo $config['footer_info_link_hover_color']; ?>}
<?php endif; ?>

<?php if ( !empty($config['price_color']) ) : ?>
.price-box .price {color:<?php echo $config['price_color']; ?>}
.products-grid .hover .price-box {background-color:<?php echo $config['price_color']; ?>}
<?php endif; ?>

<?php if ( !empty($config['price_circle_color']) ) : ?>
.products-grid .hover .price-box {background-color:<?php echo $config['price_circle_color']; ?>}
<?php endif; ?>

/**~~ helper classes ~~**/
<?php if ( $config['enable_font'] == 1 ) : ?>
.shopper-font {font-family:"<?php echo $config['font']; ?>"}
<?php endif; ?>
<?php if ( !empty($config['color']) ) : ?>
.shopper-color {color:<?php echo $config['color']; ?>}
.shopper-bgcolor {background-color:<?php echo $config['color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['title_color']) ) : ?>
.shopper-titlecolor {color:<?php echo $config['title_color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['menu_text_color']) ) : ?>
.shopper-menucolor {color:<?php echo $config['menu_text_color']; ?>}
<?php endif; ?>
<?php if ( !empty($config['content_bg']) ) : ?>
.shopper-content_bg {background-color:<?php echo $config['content_bg']; ?>}
<?php endif; ?>
<?php if ( !empty($config['content_link']) ) : ?>
.shopper-content_link {color:<?php echo $config['content_link']; ?>}
<?php endif; ?>
<?php if ( !empty($config['content_link_hover']) ) : ?>
.shopper-content_link_hover {color:<?php echo $config['content_link_hover']; ?>}
<?php endif; ?>
<?php if ( !empty($config['page_title_bg']) ) : ?>
.shopper-page_title_bg {background-color:<?php echo $config['page_title_bg']; ?>}
<?php endif; ?>