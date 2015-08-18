<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'smarzrxr_cinepanda');

/** MySQL database username */
define('DB_USER', 'smarzrxr_panda');

/** MySQL database password */
define('DB_PASSWORD', 'smartersworld');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'eM<,ZkeDJ;#&p<0BW.S@3d62ju )Nf<n}N`~b3z`biylb?zWnE<9|9<Y_pR#ET^8');
define('SECURE_AUTH_KEY',  '5}(06NZvKI|P-YCWr;1fJa;8<qU/LoBKhN9jYVuW/lJS{/2KJy||N-49JHd5@F>^');
define('LOGGED_IN_KEY',    'lN38!%f+b|4xl{Pj`%9|+O(ALLH+e2(sOj>x5UztW2TM#R@Nt+wo(J|_vfE*yz[?');
define('NONCE_KEY',        'CDD^r-D`rq4LFTyhPNEr`DF>d|k,WQ#3+h=+|5*eMQ[X+Dl:q;l~$[,%3b79HB=Z');
define('AUTH_SALT',        'g}_ /Vm;Yz,eI-7RG&XyiXr1=[Q3+:`MFE+fp+/%+Opo)$+iYf7rAhN[YKedxg~m');
define('SECURE_AUTH_SALT', 'wXTAriHf0sEzSBw/!qC1i/AulK1*eiNMN#*G%+_n<>q/1bj29)?I,$,u{_(|3650');
define('LOGGED_IN_SALT',   '=UxH2FLGxO8J<VF2edoGeqr$p9ZRVPIExVclk=]Q/{A&&0cy5(;+#@K-9mg-)K75');
define('NONCE_SALT',       '(bWvQ /|u!>pj<1--t+{~^r,+Jedywt]<GAiOUDEx#.G1a)eBk-1mY>VT})Ol}6z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
