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
define( 'DB_NAME', 'vubatpfc' );

/** Database username */
define( 'DB_USER', 'vubatpfc' );

/** Database password */
define( 'DB_PASSWORD', '*8U58rFF(5ijWg' );

/** Database hostname */
define( 'DB_HOST', 'db' );

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
define( 'AUTH_KEY',          'Q/k2<qOI5M:skTG2LESX,i5H%=of6[-tGNF`.I^D<fJY7cv4Ok.,iO<9hEA/hs3k' );
define( 'SECURE_AUTH_KEY',   '5r_fS]d0{a:A,0|*p_<V?E[VtYcCJ(JI>V4N0{x0xd}~}P6uFY_n13 =&U>$o]{S' );
define( 'LOGGED_IN_KEY',     'ijSaIZt4`8me1nZw]0}Zq]5k1!s?oZsg`N09a|cElGhZy1c7c4B+H1a|Ku&4Aw,Z' );
define( 'NONCE_KEY',         'i.fSiqOHH_x:#VP`E&#cY#Y/fc1sxwy66$_Jl<f6*f@ce(Fj@^.h8 {lG%$EY0a;' );
define( 'AUTH_SALT',         'w;CxtPV&jPEsJ)>ZN?=~]U2rbInXMhF%0Ms Y6M`qAZV2eEG4+/a+:p[ZL*)y vQ' );
define( 'SECURE_AUTH_SALT',  'ca^OyRL--3?KcPGa4{tAz8+T(z{R{,;9<nr8!?.~k`:?oR]960iBrj_G/k<Em;Lq' );
define( 'LOGGED_IN_SALT',    'Z#J20Mb(>d698wzyZn;?IC} +a5Hm?{UThP6lhi=_v&6X0B>ro[`pAFfq1,C68<j' );
define( 'NONCE_SALT',        '5iB4x|UyXsx>[Q2FKfv3_zCBKMHhpt76X:#,`<r$^W[,N3c>Gs1ATOf[h.8}EHcr' );
define( 'WP_CACHE_KEY_SALT', '2Tp[EoVqQxA>j%S5|)hgsP)D:,,#pT]YwIa2?|nPYS_At--]BVo>LTSfQ{w6dse4' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpvividstg01_';


/* Add any custom values between this line and the "stop editing" line. */



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
