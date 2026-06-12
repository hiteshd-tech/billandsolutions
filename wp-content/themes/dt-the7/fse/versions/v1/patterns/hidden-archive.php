<?php
/**
 * Title: archive
 * Slug: dt-the7/hidden-archive
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Header Wrapper"},"style":{"position":{"type":"sticky","top":"0px"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0">
    <!-- wp:template-part {"slug":"header","tagName":"header"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Archive Title"},"style":{"spacing":{"padding":{"top":"0px","bottom":"0px"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":""},"background":{"backgroundImage":{"url":"<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/corner-top-right-fr.svg", "title":"corner-top-right-fr"},"backgroundSize":"75%","backgroundPosition":"100% 0%","backgroundRepeat":"no-repeat"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color"
     style="margin-top:0;margin-bottom:0;padding-top:0px;padding-bottom:0px">
    <!-- wp:group {"metadata":{"name":"Title Layout"},"style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
    <div class="wp-block-group"
         style="min-height:0px;padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
        <!-- wp:query-title {"type":"archive","textAlign":"center","showPrefix":false,"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"textColor":"contrast-content","fontSize":"5-x-large"} /-->

        <!-- wp:term-description {"textAlign":"center","fontSize":"large"} /--></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"posts-loop"} /-->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->