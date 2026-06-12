<?php
/**
 * Search form template.
 *
 * @since 1.0.0
 *
 * @package The7\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="searchform" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="the7-search" class="screen-reader-text"><?php esc_html_e( 'Search:', 'the7mk2' ); ?></label>
    <input type="text" id="the7-search" class="field searchform-s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Type and hit enter &hellip;', 'the7mk2' ); ?>" />
    <input type="submit" class="assistive-text searchsubmit" value="<?php esc_attr_e( 'Go!', 'the7mk2' ); ?>" />
    <a href="" class="submit"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve"><path d="M11.7,10.3c2.1-2.9,1.5-7-1.4-9.1s-7-1.5-9.1,1.4s-1.5,7,1.4,9.1c2.3,1.7,5.4,1.7,7.7,0h0c0,0,0.1,0.1,0.1,0.1l3.8,3.8c0.4,0.4,1,0.4,1.4,0s0.4-1,0-1.4l-3.8-3.9C11.8,10.4,11.8,10.4,11.7,10.3L11.7,10.3z M12,6.5c0,3-2.5,5.5-5.5,5.5S1,9.5,1,6.5S3.5,1,6.5,1S12,3.5,12,6.5z"/></svg></a>
</form>
