<?php
/**
 * Migrations in progress tab.
 *
 * @package The7/Dev/Templates
 */

defined( 'ABSPATH' ) || exit;

$is_locked = '';
if ( The7_Install::get_updater()->is_locked() ) {
    $is_locked = ' (in progress)';
}
?>
<h2>Migrations Queued<?php echo ( $is_locked ? '<span style="color: red;">' . esc_html( $is_locked ) . '</span>' : '' ); ?></h2>

<?php
$batches = The7_Install::get_updater()->get_batches();

if ( $batches ) {
	echo '<pre>';
	var_dump( $batches );
	echo '</pre>';
} else {
	echo '<p>All migrations are done.</p>';
}
