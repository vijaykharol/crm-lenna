<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hipldemo1_wp_8ptaf' );

/** Database username */
define( 'DB_USER', 'hipldemo1_wp_04w8r' );

/** Database password */
define( 'DB_PASSWORD', 'BKA9JMaXE~6Ol?n2' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY', 'fe24S22!l6Gf(9-i2f6YbtPQBlUc|qgIUu!H%2)[)iYz11y*5/R~Q-Ya;49CtC4D');
define('SECURE_AUTH_KEY', 'JiL#v4M-0IRV-PRXB98hB03r-5-5he1si3_zDw9h_I77yoLXg4i~c60n4[5yR6v8');
define('LOGGED_IN_KEY', '3RY!yH;~9s5[750w7z]#&xt3a+N1v9)HPPQTv328u2EBQzc/vJE14T~HnSA&7bVI');
define('NONCE_KEY', 'fp(0727o)lA*|86;F3m-[Bzu%+(@8[-l;Ii]P;/QMRx52]#p~R);LzVY773W8t*A');
define('AUTH_SALT', 'b3Z&Km9@#1MT9B3[9sY+q_&eOZgF6AI3qA[w&5-U1@6xFSF06Cz8*8_uSs-65x(U');
define('SECURE_AUTH_SALT', '3&~~o_gw9CBuP]WBi4!|-FZ0|lh_]B(lC|3&A+8X3v|*Ia3+7Q9Yn2f%+h_D(937');
define('LOGGED_IN_SALT', '3W)v@H-wSmCfg6BZg|R6(gof[(7hx*44IrM7i891AoS81F/~wc#b4t;B|7~4x1A#');
define('NONCE_SALT', '35*S1;eF:[ErS%2EVd1+5-LY47+E3g~9YfO[]XUp!SR886_062%795N1B1r(~34H');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'AB1SWM_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
