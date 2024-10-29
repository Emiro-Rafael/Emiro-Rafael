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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define('FS_METHOD','direct');
// ** MySQL settings - You can get this info from your web host ** //

if($_SERVER['HTTP_HOST'] && $_SERVER['HTTP_HOST'] != ''){
	define("SITE_HOSTNAME", $_SERVER['HTTP_HOST']);
} else {
	define("SITE_HOSTNAME", $_SERVER['REQUEST_URI']);
}
switch (SITE_HOSTNAME) {
    default:
    	/** The name of the database for WordPress */
		define( 'DB_NAME', 'candybar_wpdb' );
		/** MySQL hostname */
		define( 'DB_HOST', 'snackcrate-db-cluster.cluster-ce1hytyinm6e.us-east-2.rds.amazonaws.com' );
	break;
	case 'candybar.snackcrate.com':
       	/** The name of the database for WordPress */
		define( 'DB_NAME', 'candybar_wpdb' );
		/** MySQL hostname */
		define( 'DB_HOST', 'snackcrate-db-cluster.cluster-ce1hytyinm6e.us-east-2.rds.amazonaws.com' );
    break;   
	case 'candybar-test.snackcrate.com':
		/** The name of the database for WordPress */
	 define( 'DB_NAME', 'candybar_wpdb' );
	 /** MySQL hostname */
	 define( 'DB_HOST', 'snackcrate-db-cluster.cluster-ce1hytyinm6e.us-east-2.rds.amazonaws.com' );
 	break;   
	case 'candybar-dev.snackcrate.com':
      	/** The name of the database for WordPress */
		define( 'DB_NAME', 'candybar-wpdb' );
		/** MySQL hostname */
		define( 'DB_HOST', 'localhost' );
 	break;    
	case 'candybar-staging.snackcrate.com':
     	/** The name of the database for WordPress */
		define( 'DB_NAME', 'candybar_staging' );
		/** MySQL hostname */
		define( 'DB_HOST', 'localhost' );
     include_once __DIR__ . DIRECTORY_SEPARATOR . "keys" . DIRECTORY_SEPARATOR . "prodtest.php";
    break;    
}

/** MySQL database username */
define( 'DB_USER', 'snackadmin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Shk=QsW5X7M5?GPttPZ' );

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
define( 'AUTH_KEY',         'Lh3K,-_yZ3sQw2qWQggt1Uf@+ql`uf[t5JC{,gKN2EVD.2,a@T?.@r& Ka(P=0w-' );
define( 'SECURE_AUTH_KEY',  'X-35*ZNvlXu6@_aE6yEPepxa5|XFUZ=t2q)ig1du>wNf&3HdkH_4YcRA&AeWZG9|' );
define( 'LOGGED_IN_KEY',    'g+/MDFN4f!<ymR{aB!c=iQPma!2GE q62cX{PP{HE}9LJk3frg<+v?@#4S}p*!G[' );
define( 'NONCE_KEY',        'iMf <Y.ex@-_~(fIQWNs<Rr<kJe$99NPg2D(A76qT<W{R=66@p6Wo3}+(([h1`}Z' );
define( 'AUTH_SALT',        '34wjnXJO0!~Jmqrq*0 Y-qBW)<qGYbIf Ac=v~jG$t9Na&bPxGTS_fFuck2p]sA^' );
define( 'SECURE_AUTH_SALT', '`]pnr4ye3S T::p5O;D2j^}NRXzd5qgUR7y[ ~a7pX?UL^4U1B^9.7+Y:kCd^.X2' );
define( 'LOGGED_IN_SALT',   'V_qyh=v0v~hre7=+1v6&_9=,bh?DWw7mWEq]x7[,>)oFe|H*I30klQ8PM3h:b_k~' );
define( 'NONCE_SALT',       'fxXAJ5D9MCQ{An;x=&|;SgczbY`_7:K;Tg)$4vVyU!OE0d,uyK7Pwaw$|V?kaOi$' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
