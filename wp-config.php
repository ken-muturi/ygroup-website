<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'blog');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '=EWjY+_!{eT#e48J}7zX8)TfdW67POqo z7?D=KLlf,eR@sf2g,jQF_RV?eaDKAZ');
define('SECURE_AUTH_KEY',  'gk3BMchNOAf.&H0kXSttb4%4a@^}]VNTL>~Phbi4=`{V>b;BaGZ<eN]g7Q><Q$)}');
define('LOGGED_IN_KEY',    'fA[QJ`#r6>WG`8}y2>Cx?&nH9kl*02SR<Jjz^jm^ksSR); s<08d>kcNmj0K-yMz');
define('NONCE_KEY',        'nNvWV8`4MLpt(NZiK{;(!B7D:qqA$yBA:5CPv-pf<il:(TSIM1]IC2Ce4+aDe}8<');
define('AUTH_SALT',        'd=6k>4GV?+vvH9-CnomD:UmY yekWO^#l7gi9m;_R5dPnzLxt@)3bvf69?2hPboU');
define('SECURE_AUTH_SALT', '5F|3tH$`j/PC_uqhbc>sv`w]n}#4~gSZs}tPGJ=}|Fa]A^sx/C]g[Jw[*n5/h|&%');
define('LOGGED_IN_SALT',   'N{?,Bu]hxt+qidW){J&_i6,.1GlH4,+*9A%<0b>+h6}zHZZ[.h}!,@a` vg&zX!)');
define('NONCE_SALT',       'cy>tz`s~;A@*)bdK$wqjR;u#u| &IGAb89oXE;Ddy#^=5aNEU)~n$`=wT)8h93Q~');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
