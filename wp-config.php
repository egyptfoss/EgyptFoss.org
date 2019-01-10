<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
require_once(ABSPATH . 'db-config.php');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         't[|rd-m[]X=1x~&Db?m^D4FHN7^GCFN9VN7!0#TZ8b)1B%[zp(LJc:M9@(n@ O0Y');
define('SECURE_AUTH_KEY',  '}L)b%.xt-,:zxwhc|-q|zgl^M]yni>O_Y.4f-ym;u6[mo.x4|]+ll!Ko!>>4?31a');
define('LOGGED_IN_KEY',    'z3lL]Kg}sfbR$:KHG5,#pm@k47u|v{M|vDKa^Zz1B!(~u|~Bm9o7snm>O,H`@,;B');
define('NONCE_KEY',        '<p&.KZCn+O=!f=R($0O*wG-YA@t+F&|9Pr#q:OX&^&a0-NteQ(_4vF-X;:Yj=X*K');
define('AUTH_SALT',        'sZ$pe81><z0LPyOz]G}scE/MVv5BZ,9nD;hK?z>wS~%%47_la*NvE[zNrsnt/ju2');
define('SECURE_AUTH_SALT', 'bvV*=wcf(08-Q6K_Q+HCl!xwu3p;cLJt:OoTl?`d4+`uk|Wsbg54Kl||-x#YyJ{R');
define('LOGGED_IN_SALT',   '!Aojv1+{H.S24YK6`i#oc*L9a,I^0va`D.K[gYpLF|Nw-~v(Vg4I|yKZ-7$wxw+C');
define('NONCE_SALT',       '0D^PV!nCNP@l|WZrOgVikJ h|a|y#7~|m1fo^u3].kxf{fZqd|2=n/0*3eR[{cR>');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpRuvF8_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define( 'ALLOW_UNFILTERED_UPLOADS', true );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
