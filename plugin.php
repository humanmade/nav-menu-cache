<?php
/**
 * Plugin Name:  HM WP Redis - Cached Nav Menus
 * Plugin URI:   https://hmn.md
 * Description:  Caching of WordPress nav menus. Requires use of WP Redis plugin by Human Made.
 * Version:      1.0.0
 * Author:       Human Made Limited
 * Author URI:   https://hmn.md
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  hm-gb
 * Domain Path:  /languages
 */

namespace HM\Nav_Menu_Cache;

require_once __DIR__ . '/inc/namespace.php';

add_action( 'init', __NAMESPACE__ . '\\bootstrap' );
