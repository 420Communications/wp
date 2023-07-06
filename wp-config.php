<?php
define( 'WP_CACHE', true );

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

 * * ABSPATH

 *

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */



// ** Database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', 'db_420' );



/** Database username */

define( 'DB_USER', 'user_420' );



/** Database password */

define( 'DB_PASSWORD', '&}p+j&A8YW!5' );



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

define( 'AUTH_KEY',         '%O9~MPkb`VSOIgw0cqlSML0{`JNLy@n9lEB+<4$4o?pG!8NYc2q|JrZ)<++lO/z$' );

define( 'SECURE_AUTH_KEY',  'Id7}-UdrzQHhl`-bEDeh]xUJ^<3T@!3&2e/A`f8(b<u.vOrFb4~wIm9vcAN5q)j<' );

define( 'LOGGED_IN_KEY',    '#G^*,-7e)#]g@]2[4|ffA_a>HAmNZ/tF=<<H5jNb>F)BVz1G*`!@8(6Z&[y(u+h<' );

define( 'NONCE_KEY',        '-fH#ik&2rDy~hH]UL)XnktaP9lQ7^Bqo/`Z{5/.3/.>kiC2lH!R>+jp^~FmiK1*X' );

define( 'AUTH_SALT',        '|Lr5GFH{q`dVK_2!Th#g~bN[B`cPa25q!(R:3UT#Nqhz}vrL=Q]NX*R[V]~88;~|' );

define( 'SECURE_AUTH_SALT', 'Xj&])o^%: y$,bK(aQ~Gg{sC=CE@Z-+8ln[5RIhH?1fPa0lr56&Q]C~`AVci4PRx' );

define( 'LOGGED_IN_SALT',   'VLfo+lOFU_d2MGDl[,VH9]*WC|>0{JixPD9ZSX={Vu`+fLIA$QG;f),ghzr,]^w$' );

define( 'NONCE_SALT',       '-^}#Er!IsVy2l/mAruY0=ahO4_VsV_.kSAuh/d~#yu|_KEGmhv#j~mAn]}ghx#<9' );



/**#@-*/



/**

 * WordPress database table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wpf_';



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

define( 'WP_DEBUG', false );



/* Add any custom values between this line and the "stop editing" line. */







/* That's all, stop editing! Happy publishing. */



/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}



/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

