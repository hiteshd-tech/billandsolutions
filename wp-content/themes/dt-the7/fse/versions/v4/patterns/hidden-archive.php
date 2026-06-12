<?php
/**
 * Title: archive
 * Slug: dt-the7/hidden-archive
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"metadata":{"name":"Archive Title"},"style":{"spacing":{"padding":{"top":"0px","bottom":"0px"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"dimensions":{"minHeight":""},"elements":{"link":{"color":{"text":"var:preset|color|bbe-neutral-300"}},"heading":{"color":{"text":"var:preset|color|bbe-neutral-000"}}},"border":{"radius":"0px"}},"backgroundColor":"bbe-primary-950","textColor":"bbe-neutral-300","layout":{"type":"constrained","contentSize":"","wideSize":""}} -->
<div class="wp-block-group has-bbe-neutral-300-color has-bbe-primary-950-background-color has-text-color has-background has-link-color" style="border-radius:0px;margin-top:0;margin-bottom:0;padding-top:0px;padding-bottom:0px"><!-- wp:group {"metadata":{"name":"Title Min Width + Vertical Paddings"},"style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"var:preset|spacing|bbe-60","padding":{"top":"var:preset|spacing|bbe-130","bottom":"var:preset|spacing|bbe-130"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group" style="min-height:0px;padding-top:var(--wp--preset--spacing--bbe-130);padding-bottom:var(--wp--preset--spacing--bbe-130)"><!-- wp:query-title {"type":"archive","textAlign":"center","showPrefix":false,"fontSize":"bbe-title-2"} /-->

<!-- wp:term-description {"textAlign":"center","fontSize":"bbe-x-large"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Posts"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|bbe-150","bottom":"var:preset|spacing|bbe-150"},"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--bbe-150);padding-bottom:var(--wp--preset--spacing--bbe-150)"><!-- wp:query {"queryId":28,"query":{"perPage":4,"pages":0,"offset":"0","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"metadata":{"categories":["blog"],"patternName":"core/block/1006"},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide"><!-- wp:group {"metadata":{"name":"Blocks Spacings"},"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|bbe-110"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:query-no-results {"align":"wide"} -->
<!-- wp:paragraph -->
<p>No posts were found.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|bbe-110"}},"layout":{"type":"grid","columnCount":null,"minimumColumnWidth":"300px"},"wpbbeResponsive":{"breakpoint":"tablet"}} -->
<!-- wp:group {"metadata":{"name":"Post Layout"},"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|bbe-30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","width":"100%","height":"","sizeSlug":"large","style":{"color":{"duotone":"unset"},"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"margin":{"bottom":"var:preset|spacing|bbe-60"}}}} /-->

<!-- wp:post-date {"style":{"elements":{"link":{"color":{"text":"var:preset|color|bbe-neutral-500"}}},"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.3"}},"textColor":"bbe-neutral-500","fontSize":"bbe-x-small"} /-->

<!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"lineHeight":"1.3"}},"fontSize":"bbe-title-4"} /-->

<!-- wp:group {"metadata":{"name":"Categories"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|bbe-40"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--bbe-40)"><!-- wp:post-terms {"term":"category","style":{"elements":{"link":{"color":{"text":"var:preset|color|bbe-primary-600"},":hover":{"color":{"text":"var:preset|color|bbe-primary-700"}}}},"spacing":{"padding":{"top":"0.3em","bottom":"0.3em","left":"0.55em","right":"0.45em"}},"typography":{"fontStyle":"normal","fontWeight":"500","textTransform":"uppercase","letterSpacing":"0.03em","fontSize":"12px"}},"backgroundColor":"bbe-primary-100","textColor":"bbe-primary-600"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal","flexWrap":"wrap"}} -->
<!-- wp:query-pagination-previous {"label":"Previous Page"} /-->

<!-- wp:query-pagination-next {"label":"Next Page"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->