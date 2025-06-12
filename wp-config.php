<?php

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
define('WP_CACHE', true);


define( 'WPCACHEHOME', 'C:\xampp\htdocs\fstudy\contents\plugins\wp-super-cache/' );
// Load environment variables from .env
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_HOST', $_ENV['DB_HOST']);

define('WP_HOME', $_ENV['WP_HOME']);
define('WP_SITEURL', $_ENV['WP_SITEURL']);

define('WP_CONTENT_FOLDERNAME', $_ENV['WP_CONTENT_FOLDERNAME']);
define('WP_CONTENT_DIR', ABSPATH . WP_CONTENT_FOLDERNAME);
define('WP_CONTENT_URL', $_ENV['WP_HOME'] . '/' . WP_CONTENT_FOLDERNAME);
define('URL_NODE_API', $_ENV['URL_NODE_API']);
define('URL_PYTHON_API', $_ENV['URL_PYTHON_API']);



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
define( 'AUTH_KEY',         'NU%lp9~8)9HoE1/FSDL`r7sX94m`,b@.)hfl=-&,f^`,G3NnGU1Dg|89fF~vae5o' );
define( 'SECURE_AUTH_KEY',  '4ZQ;*[.M9ip|UE0Ld!|B[GYgozHhEgwnHr/eHTrl=kqRT6{`pZDic.%w9p3FC$13' );
define( 'LOGGED_IN_KEY',    'K,$%.5}u`su(8 F3?uf*yZD~BdUYlB>eH++H[RO8G`0Hk78Xz*ajlRVVv@|P<&&!' );
define( 'NONCE_KEY',        'SRgiI{1{[j~R5LtY&-W%QA@DmBZiKo]RD2!iZ(Jsx!Ah&Rb4dx4Tz5kewV[[IY~J' );
define( 'AUTH_SALT',        '!DkU?O$!MMkc._u,a6dnFMx;7fD#j^C9jOPoW>:DmbtBc8CSu5l{/4e{2un?QN6L' );
define( 'SECURE_AUTH_SALT', '&.(:^8-N pXg{<~hbFTr *;BQ59:}%vW*bh(IUp.0`6b$9^tC;nKyXQE9+(3Zh[X' );
define( 'LOGGED_IN_SALT',   ' ?x|uUjG*`qb@tMAIm2.`Zqr4.o0kM(R1m(I8[Z6sBn^Bq}]QROFT=^k@cZYJd-L' );
define( 'NONCE_SALT',       'G.P#ctHZ3pXs|p/+*F>PjAe1V~0Tx[iytKr^8}a6t&B?_rOGo(8$x9y}!o7`gZZk' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define('WP_DEBUG_DISPLAY', false);


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);






/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

