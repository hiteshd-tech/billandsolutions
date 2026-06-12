<?php
/**
 * Title: 404
 * Slug: dt-the7/hidden-404
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"404 Layout"},"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|100","bottom":"var:preset|spacing|100","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"100dvh"},"background":{"backgroundImage":{"url":"<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/corner-top-right-fr.svg", "title":"corner-top-right-fr"},"backgroundPosition":"100% 0%","backgroundSize":"75%","backgroundRepeat":"no-repeat"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color"
     style="min-height:100dvh;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--100);padding-right:0;padding-bottom:var(--wp--preset--spacing--100);padding-left:0">
    <!-- wp:heading {"textAlign":"center","level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}},"typography":{"fontSize":"10rem","lineHeight":"1"}},"textColor":"contrast-content"} -->
    <h1 class="wp-block-heading has-text-align-center has-contrast-content-color has-text-color has-link-color"
        style="font-size:10rem;line-height:1">404</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"500"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"textColor":"contrast-content","fontSize":"3-x-large"} -->
    <p class="has-text-align-center has-contrast-content-color has-text-color has-link-color has-3-x-large-font-size"
       style="font-style:normal;font-weight:500">Page not found</p>
    <!-- /wp:paragraph -->

    <!-- wp:spacer {"height":"0px","style":{"layout":{"flexSize":"1rem","selfStretch":"fixed"}}} -->
    <div style="height:0px" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:buttons {"style":{"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap","orientation":"horizontal"}} -->
    <div class="wp-block-buttons">
        <!-- wp:button {"textAlign":"center","style":{"spacing":{"padding":{"left":"2.4em","right":"2.4em","top":"1.1em","bottom":"1.1em"}}},"fontSize":"large"} -->
        <div class="wp-block-button has-custom-font-size has-large-font-size"><a
                    class="wp-block-button__link has-text-align-center wp-element-button" href="#"
                    style="padding-top:1.1em;padding-right:2.4em;padding-bottom:1.1em;padding-left:2.4em">Visit
                homepage</a></div>
        <!-- /wp:button --></div>
    <!-- /wp:buttons --></div>
<!-- /wp:group -->