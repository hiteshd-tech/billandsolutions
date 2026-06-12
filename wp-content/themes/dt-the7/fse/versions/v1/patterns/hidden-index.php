<?php
/**
 * Title: index
 * Slug: dt-the7/hidden-index
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Header Wrapper"},"style":{"position":{"type":"sticky","top":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:template-part {"slug":"header","tagName":"header"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Blog Title"},"style":{"background":{"backgroundImage":{"url":"<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/corner-top-right-fr.svg", "title":"corner-top-right-fr"},"backgroundSize":"75%","backgroundPosition":"100% 0%","backgroundRepeat":"no-repeat"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color">
    <!-- wp:group {"metadata":{"name":"Title Layout"},"align":"wide","style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
    <div class="wp-block-group alignwide"
         style="min-height:0px;padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
        <!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"textColor":"contrast-content","fontSize":"5-x-large"} -->
        <h2 class="wp-block-heading has-contrast-content-color has-text-color has-link-color has-5-x-large-font-size">
            Our Blog</h2>
        <!-- /wp:heading --></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"posts-loop","area":"uncategorized"} /-->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->