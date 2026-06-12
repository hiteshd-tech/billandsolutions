<?php
/**
 * Title: footer
 * Slug: dt-the7/hidden-footer
 * Inserter: no
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"},"blockGap":"var:preset|spacing|80"}},"backgroundColor":"background-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background-2-background-color has-background"
     style="padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
    <!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|80","left":"var:preset|spacing|80"}}},"wpbbeResponsive":{"breakpoint":"tablet"}} -->
    <div class="wp-block-columns alignwide">
        <!-- wp:column {"width":"40%","style":{"spacing":{"blockGap":"var:preset|spacing|60"}}} -->
        <div class="wp-block-column" style="flex-basis:40%">
            <!-- wp:image {"width":"180px","sizeSlug":"large","linkDestination":"none"} -->
            <figure class="wp-block-image size-large is-resized"><img
                        src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/LG-bot-01-1.svg"
                        alt="" class="" style="width:180px"/></figure>
            <!-- /wp:image --></div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"31.325%","style":{"spacing":{"blockGap":"var:preset|spacing|60"}}} -->
        <div class="wp-block-column" style="flex-basis:31.325%">
            <!-- wp:heading {"level":6,"style":{"elements":{"link":{"color":{"text":"var:preset|color|headings"}}},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"0.1em"}},"textColor":"headings","fontSize":"small"} -->
            <h6 class="wp-block-heading has-headings-color has-text-color has-link-color has-small-font-size"
                style="font-style:normal;font-weight:700;letter-spacing:0.1em;text-transform:uppercase">Contact
                info</h6>
            <!-- /wp:heading -->

            <!-- wp:group {"style":{"layout":{"selfStretch":"fixed","flexSize":"30%"},"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group">
                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-titles"}}}},"textColor":"custom-titles","fontSize":"medium"} -->
                <p class="has-custom-titles-color has-text-color has-link-color has-medium-font-size">Call us:
                    1-001-234-5678</p>
                <!-- /wp:paragraph -->

                <!-- wp:paragraph -->
                <p><a href="mailto:info@mywebsite.com">info@mywebsite.com</a></p>
                <!-- /wp:paragraph -->

                <!-- wp:paragraph -->
                <p>Office: 3 Rockaway St., New Rochelle, NY 10801</p>
                <!-- /wp:paragraph -->

                <!-- wp:spacer {"height":"0px","style":{"layout":{"selfStretch":"fixed","flexSize":"0.15rem"}}} -->
                <div style="height:0px" aria-hidden="true" class="wp-block-spacer"></div>
                <!-- /wp:spacer -->

                <!-- wp:social-links {"iconColor":"custom-titles","iconColorValue":"#041937","openInNewTab":true,"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                <ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-logos-only">
                    <!-- wp:social-link {"url":"#","service":"x"} /-->

                    <!-- wp:social-link {"url":"#","service":"facebook"} /-->

                    <!-- wp:social-link {"url":"#","service":"youtube"} /-->

                    <!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
                <!-- /wp:social-links --></div>
            <!-- /wp:group --></div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"25%","style":{"spacing":{"blockGap":"var:preset|spacing|60"}}} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|headings"}}},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"0.1em"}},"textColor":"headings","fontSize":"small"} -->
            <p class="has-headings-color has-text-color has-link-color has-small-font-size"
               style="font-style:normal;font-weight:700;letter-spacing:0.1em;text-transform:uppercase">Company</p>
            <!-- /wp:paragraph -->

            <!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group">

                <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"medium","wpbbeOverlayMenu":[]} -->

                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Home', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'About', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Blog', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Services', 'the7mk2' ); ?>","url":"#"} /-->

                <!-- /wp:navigation -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"25%","style":{"spacing":{"blockGap":"var:preset|spacing|60"}}} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:heading {"level":6,"style":{"elements":{"link":{"color":{"text":"var:preset|color|headings"}}},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"0.1em"}},"textColor":"headings","fontSize":"small"} -->
            <h6 class="wp-block-heading has-headings-color has-text-color has-link-color has-small-font-size"
                style="font-style:normal;font-weight:700;letter-spacing:0.1em;text-transform:uppercase">Legals &amp;
                support</h6>
            <!-- /wp:heading -->

            <!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group">

                <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"medium","wpbbeOverlayMenu":[]} -->

                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Contact', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Terms & conditions', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Design system', 'the7mk2' ); ?>","url":"#"} /-->
                <!-- wp:navigation-link {"label":"<?php esc_html_e( 'Search results', 'the7mk2' ); ?>","url":"#"} /-->

                <!-- /wp:navigation -->

            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column --></div>
    <!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"dimensions":{"minHeight":""},"border":{"top":{"color":"var:preset|color|separators","width":"1px"},"right":{"width":"0px","style":"none"},"bottom":{"width":"0px","style":"none"},"left":{"width":"0px","style":"none"}},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"backgroundColor":"background-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background-2-background-color has-background"
     style="border-top-color:var(--wp--preset--color--separators);border-top-width:1px;border-right-style:none;border-right-width:0px;border-bottom-style:none;border-bottom-width:0px;border-left-style:none;border-left-width:0px;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
    <!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group alignwide">
        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|content-2"}}}},"textColor":"content-2","fontSize":"small"} -->
        <p class="has-content-2-color has-text-color has-link-color has-small-font-size">© Dream-Theme, 2024. All rights
            reserved.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|content-2"}}}},"textColor":"content-2","fontSize":"small"} -->
        <p class="has-content-2-color has-text-color has-link-color has-small-font-size">Powered by The7 FSE.</p>
        <!-- /wp:paragraph --></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->