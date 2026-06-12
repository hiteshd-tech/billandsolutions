<?php
/**
 * Title: search
 * Slug: dt-the7/hidden-search
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Header Wrapper"},"style":{"position":{"type":"sticky","top":"0px"},"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:template-part {"slug":"header","tagName":"header"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Search Title"},"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}},"spacing":{"padding":{"top":"0","bottom":"0"},"blockGap":"0"},"border":{"radius":"0px"}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color" style="border-radius:0px;padding-top:0;padding-bottom:0"><!-- wp:group {"metadata":{"name":"Title Layout"},"align":"wide","style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"var:preset|spacing|91","bottom":"var:preset|spacing|91"}},"border":{"radius":"0px"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"stretch"}} -->
<div class="wp-block-group alignwide" style="border-radius:0px;min-height:0px;padding-top:var(--wp--preset--spacing--91);padding-bottom:var(--wp--preset--spacing--91)"><!-- wp:query-title {"type":"search","textAlign":"center","level":2,"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-headings"}}}},"textColor":"contrast-headings","fontSize":"5-x-large"} /-->

<!-- wp:group {"metadata":{"name":"Search Field"},"style":{"elements":{"button":{"color":{"text":"var:preset|color|background-1","background":"var:preset|color|accent-2"}}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:search {"label":"","showLabel":false,"placeholder":"Search...","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"style":{"border":{"radius":"5px"}},"backgroundColor":"accent-1","fontSize":"small","borderColor":"background-1"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Search Results"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|91","bottom":"var:preset|spacing|91"},"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--91);padding-bottom:var(--wp--preset--spacing--91)"><!-- wp:query {"queryId":28,"query":{"perPage":4,"pages":0,"offset":"0","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":true},"metadata":{"categories":["blog"],"patternName":"core/block/1006"},"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-query alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|90"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:query-no-results {"align":"wide"} -->
<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|content-2"}}}},"textColor":"content-2"} -->
<p class="has-content-2-color has-text-color has-link-color">No posts were found.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|80"}},"layout":{"type":"default","columnCount":null,"minimumColumnWidth":"380px"}} -->
<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|46","left":"var:preset|spacing|50"}}},"wpbbeResponsive":{"breakpoint":"custom","breakpointCustomValue":"650px","settings":{"reverseOrder":true}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":""} -->
<div class="wp-block-column"><!-- wp:group {"style":{"layout":{"selfStretch":"fit","flexSize":null},"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|headings"}}}},"textColor":"headings","fontSize":"3-x-large"} /-->

<!-- wp:post-excerpt {"excerptLength":30,"fontSize":"small"} /-->

<!-- wp:read-more {"content":"Learn More"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"150px"} -->
<div class="wp-block-column" style="flex-basis:150px"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","width":"150px","height":"150px","style":{"color":{"duotone":"unset"},"layout":{"selfStretch":"fill","flexSize":null}}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal","flexWrap":"wrap"}} -->
<!-- wp:query-pagination-previous {"label":"Previous Page"} /-->

<!-- wp:query-pagination-next {"label":"Next Page"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->