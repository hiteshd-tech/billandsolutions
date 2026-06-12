<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('FS_METHOD', 'direct');
define( 'DB_NAME', 'billandsolutions' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'f9,Y8C~Ci)KY4U::qj:9SxTq`W7!}rjensN;7N %u`+Vv@.Q!.#F?HeCNV~}L[rP' );
define( 'SECURE_AUTH_KEY',  'mquN(:DFxEu|HpW*TayIt-Gz>F0K6irXcxC2<5SS6s=FcG3[/Q,J%BGnFcn3sV?5' );
define( 'LOGGED_IN_KEY',    'uox56>Wwhj[JaR7s]UStO`2r|!)6#t+E##bHM*A76LyZPyeCK;8gy6RpCfKd5T!6' );
define( 'NONCE_KEY',        '5rs)`B]8WoVzA snD}E(_G%7EI=*{T{Ypw-SL(QBvq`Z4u!;m;9b<~N78}W?nK$ ' );
define( 'AUTH_SALT',        'e8xSb[y,#+e!|.xsx,ZuNIi0~6V#E7ZP8t8MO+h.=LTzoAQ=m^,G%rwK[-N#r9%n' );
define( 'SECURE_AUTH_SALT', 'Tc;`H=<M +I1khu2zjMN0 T1!Qt$B=u<J)]8W ?Ym[opf41 ~ Q-(FH0,*vCo~^8' );
define( 'LOGGED_IN_SALT',   'O.A&`:WQ7x_xu2e:9s<O}]!;]T?]j<*;`wn7-Am/W(NqCD9M>oyc#|GiZI krh3;' );
define( 'NONCE_SALT',       '!!zSz!u?`Z>ITz{zpC92=$Y&*Rx8A8f]H,/&,2ZNtb~*x6<j4jRiCTi4}m)[A{n$' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'bs_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
