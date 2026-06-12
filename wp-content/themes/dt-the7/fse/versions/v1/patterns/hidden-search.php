<?php
/**
 * Title: search
 * Slug: dt-the7/hidden-search
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Header Wrapper"},"style":{"position":{"type":"sticky","top":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:template-part {"slug":"header","tagName":"header"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Search Title"},"style":{"background":{"backgroundImage":{"url":"<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/corner-top-right-fr.svg", "title":"corner-top-right-fr"},"backgroundSize":"75%","backgroundPosition":"100% 0%","backgroundRepeat":"no-repeat"},"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"backgroundColor":"contrast-background","textColor":"contrast-content","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-contrast-content-color has-contrast-background-background-color has-text-color has-background has-link-color"><!-- wp:group {"metadata":{"name":"Title Layout"},"align":"wide","style":{"dimensions":{"minHeight":"0px"},"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"stretch"}} -->
    <div class="wp-block-group alignwide" style="min-height:0px;padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)"><!-- wp:query-title {"type":"search","textAlign":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-content"}}}},"textColor":"contrast-content","fontSize":"3-x-large"} /-->

        <!-- wp:group {"metadata":{"name":"Search Field"},"style":{"elements":{"button":{"color":{"text":"var:preset|color|background-1","background":"var:preset|color|accent-2"}}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}},"border":{"radius":"100px","width":"1px"}},"backgroundColor":"background-1","borderColor":"separators","layout":{"type":"constrained"}} -->
            <div class="wp-block-group has-border-color has-separators-border-color has-background-1-background-color has-background" style="border-width:1px;border-radius:100px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)"><!-- wp:search {"label":"","showLabel":false,"placeholder":"Search...","buttonText":"Search","style":{"border":{"radius":"100px","width":"0px","style":"none"}},"backgroundColor":"accent-1","fontSize":"medium"} /--></div>
            <!-- /wp:group --></div>
        <!-- /wp:group --></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Search Results"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|100","bottom":"var:preset|spacing|100"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--100);padding-bottom:var(--wp--preset--spacing--100)"><!-- wp:query {"queryId":28,"query":{"perPage":4,"pages":0,"offset":"0","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":true},"metadata":{"categories":["blog"],"patternName":"core/block/1006"},"align":"wide","layout":{"type":"constrained"}} -->
    <div class="wp-block-query alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|90"}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group"><!-- wp:query-no-results {"align":"wide"} -->
            <!-- wp:paragraph -->
            <p>No posts were found.</p>
            <!-- /wp:paragraph -->
            <!-- /wp:query-no-results -->

            <!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|90"}},"layout":{"type":"default","columnCount":null,"minimumColumnWidth":"380px"}} -->
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"space-between"},"wpbbeResponsive":{"breakpoint":"mobile","settings":{"orientation":"column-reverse"}}} -->
            <div class="wp-block-group" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:group {"style":{"layout":{"selfStretch":"fixed","flexSize":null}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group"><!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|headings"}}}},"textColor":"headings","fontSize":"2-x-large"} /-->

                    <!-- wp:post-excerpt {"excerptLength":30,"fontSize":"medium"} /-->

                    <!-- wp:read-more {"content":"Learn more","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-1"}}}},"textColor":"accent-1","fontSize":"small"} /--></div>
                <!-- /wp:group -->

                <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","width":"","height":"","style":{"color":{"duotone":"unset"},"layout":{"selfStretch":"fixed","flexSize":"50%"}}} /--></div>
            <!-- /wp:group -->
            <!-- /wp:post-template -->

            <!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"medium","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal","flexWrap":"wrap"}} -->
            <!-- wp:query-pagination-previous {"label":"Previous Page"} /-->

            <!-- wp:query-pagination-next {"label":"Next Page"} /-->
            <!-- /wp:query-pagination --></div>
        <!-- /wp:group --></div>
    <!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->