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

<!-- wp:group {"metadata":{"name":"Blog Title"},"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}},"border":{"radius":"0px"},"spacing":{"padding":{"top":"0","bottom":"0"}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color" style="border-radius:0px;padding-top:0;padding-bottom:0"><!-- wp:group {"metadata":{"name":"Title Layout"},"align":"wide","style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|81","bottom":"var:preset|spacing|81"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group alignwide" style="min-height:0px;padding-top:var(--wp--preset--spacing--81);padding-bottom:var(--wp--preset--spacing--81)"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-headings"}}}},"textColor":"contrast-headings","fontSize":"6-x-large"} -->
<h2 class="wp-block-heading has-contrast-headings-color has-text-color has-link-color has-6-x-large-font-size">Latest Articles</h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"posts-loop","area":"uncategorized"} /-->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->