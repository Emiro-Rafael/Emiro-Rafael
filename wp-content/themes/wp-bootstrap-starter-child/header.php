<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

global $is_redesign_page;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <meta name="description" content="Your one-stop shop for delicious snacks and candy from around the world">
    <meta property="og:image" content="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/candybar-hero1200x630.png" />
    <meta property="og:image:alt" content="A CandyBar customer being given more snacks than he can carry" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://candybar.snackcrate.com/">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<?php 
    wp_head();
?>
    <script type="text/javascript" src="https://js.stripe.com/v3"></script>
    <!--<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/main.js"></script>-->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
    <script type="text/javascript" src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script type="module" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/snack-swiper.js"></script>
<script defer>document.addEventListener('load', function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "https://widget.equally.ai/equally-widget.min.js";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'equallyWidget'));!window.EQUALLY_AI_API_KEY&&(window.EQUALLY_AI_API_KEY="j2enhbeigicjemy23w2sjzqdkx9g1jcs",intervalId=setInterval(function(){window.EquallyAi&&(clearInterval(intervalId),window.EquallyAi=new EquallyAi)},500));</script>
    <script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
    <!-- BEGIN Apple Sign in metatags -->
    <meta name="appleid-signin-client-id" content="com.snackcrate.service">
    <meta name="appleid-signin-scope" content="name email">
    <meta name="appleid-signin-redirect-uri" content="<?php echo get_stylesheet_directory_uri(); ?>/lib/AppleSignin.php">
    <meta name="appleid-signin-state" content="<?php echo $_SESSION['csrf_token'];?>">
    <meta name="appleid-signin-nonce" content="<?php echo wp_create_nonce('com.snackcrate.service');?>">
    <meta name="appleid-signin-use-popup" content="true">
    <meta name="appleid-signin-response_type" content="id_token">
    <meta name="appleid-signin-response_mode" content="form_post">
    <!-- END Apple Sign in metatags -->

    <!-- google site verification -->
    <meta name="google-site-verification" content="-vlq2zn9wNKxO9gdEMdREWuCDNO1Rm1x_O7m2peFPfg" />

    <!-- BEGIN PLERDY CODE -->
    <script type="text/javascript" defer data-plerdy_code='1'>
        var _protocol="https:"==document.location.protocol?" https://":" http://";
        _site_hash_code = "651a506708169d631bfa5955ad51c30e",_suid=31308, plerdyScript=document.createElement("script");
        plerdyScript.setAttribute("defer",""),plerdyScript.dataset.plerdymainscript="plerdymainscript",
        plerdyScript.src="https://a.plerdy.com/public/js/click/main.js?v="+Math.random();
        var plerdymainscript=document.querySelector("[data-plerdymainscript='plerdymainscript']");
        plerdymainscript&&plerdymainscript.parentNode.removeChild(plerdymainscript);
        try{document.head.appendChild(plerdyScript)}catch(t){console.log(t,"unable add script tag")}
    </script>
    <!-- END PLERDY CODE -->
    
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1721355838145991');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" 
            src="https://www.facebook.com/tr?id=1721355838145991&ev=PageView&noscript=1"/>
    </noscript>

    <!-- TikTok Analytics -->
    <script>
    !function (w, d, t) {
    w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
    ttq.load('C2OJHV23E7AJ697POOPG');
    ttq.page();
    }(window, document, 'ttq');
    </script>

    <!-- Snap Pixel Code -->
    <script type='text/javascript'>
    (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
    {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
    a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
    r.src=n;var u=t.getElementsByTagName(s)[0];
    u.parentNode.insertBefore(r,u);})(window,document,
    'https://sc-static.net/scevent.min.js');
    snaptr('init', '949086f0-251a-4368-be1d-48762569b903', {
    'user_email': '__INSERT_USER_EMAIL__'
    });
    snaptr('track', 'PAGE_VIEW');
    </script>
    <!-- End Snap Pixel Code -->

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5V474ZF');</script>
    <!-- End Google Tag Manager -->
    
    <!-- Global site tag (gtag.js) - Google Ads: 849141978 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-JKCD1HFMFG"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-JKCD1HFMFG');
    </script>
    <?php if($is_redesign_page): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
        <link rel="stylesheet" href="<?=get_stylesheet_directory_uri()?>/assets/redesign/css/style.css" type="text/css">
    <?php endif; ?>
</head>

<body <?php body_class(); ?> data-namespace="<?php echo basename(get_permalink());//get_post_field( 'post_name', get_post() );?>">
<?php if(!$is_redesign_page): ?>
<div class="loading_wrapper">
    <div class="loading">    
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/loading.gif" />
    </div>
</div>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>
    <?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
        <?php get_template_part( 'partials/secondary-header' ); ?>    
        <header id="header" class="fixed-top bg-white" role="banner">
            <?php get_template_part( 'partials/nav' ); ?>
        </header><!-- #masthead -->
        
        <script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/nav.js"></script>

	    <div id="content" class="site-content">
    <?php endif; ?>
<?php else: ?>
    <header class="header">
        <div class="container container--big">
            <div class="header__container">
                <div class="header__block">
                    <button type="button" class="header__burger">
                        <span></span>
                    </button>
                </div>
                <a href="/" class="header__logo">
                    <img src="https://www.snackcrate.com/wp-content/themes/snackcrate/home-page/img/logo.svg" alt="logo" width="67" height="37">
                </a>
                <div class="header__block">
                
                </div>
            </div>
        </div>
    </header>
<?php endif; ?>