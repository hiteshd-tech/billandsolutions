<?php
/**
 * Title: single
 * Slug: dt-the7/hidden-single
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Header Wrapper"},"style":{"position":{"type":"sticky","top":"0px"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0">
    <!-- wp:template-part {"slug":"header","tagName":"header"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Title \u0026 Meta"},"style":{"spacing":{"blockGap":"var:preset|spacing|90","padding":{"top":"0","bottom":"0"}},"background":{"backgroundImage":{"url":"<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/corner-top-right-fr.svg", "title":"corner-top-right-fr"},"backgroundPosition":"100% 0%","backgroundSize":"75%","backgroundRepeat":"no-repeat"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color"
     style="padding-top:0;padding-bottom:0">
    <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","orientation":"vertical"}} -->
    <div class="wp-block-group"
         style="padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
        <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|45"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group">
            <!-- wp:post-terms {"term":"category","textAlign":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|background-1"}}},"spacing":{"padding":{"right":"0.6em","left":"0.6em","top":"0.15em","bottom":"0.15em"}},"typography":{"textTransform":"uppercase","letterSpacing":"0.1em","fontStyle":"normal","fontWeight":"600"}},"backgroundColor":"accent-2","textColor":"background-1","fontSize":"x-small"} /-->

            <!-- wp:post-date {"textAlign":"center","displayType":"modified","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-2"}}},"typography":{"fontStyle":"normal","fontWeight":"500"}},"textColor":"accent-2","fontSize":"small"} /--></div>
        <!-- /wp:group -->

        <!-- wp:post-title {"textAlign":"center","level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"textColor":"contrast-content","fontSize":"5-x-large"} /-->
    </div>
    <!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Post Content"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-background-color has-background"
     style="padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
    <!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /-->

    <!-- wp:template-part {"slug":"tags-author"} /--></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"more-posts","area":"uncategorized"} /-->

<!-- wp:template-part {"slug":"discussion","area":"uncategorized"} /-->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->