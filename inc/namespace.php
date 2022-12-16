<?php

namespace HM\Nav_Menu_Cache;

use WP_Term;

const CACHE_KEY = 'hm-nav-menu-cache';

/**
 * Init.
 *
 * @return void
 */
function bootstrap() {
	wp_cache_add_redis_hash_groups( CACHE_KEY );

	// Get/Set menu cache.
	add_filter( 'pre_wp_nav_menu', __NAMESPACE__ . '\\filter_nav_menu_before', 10, 2 );
	add_filter( 'wp_nav_menu', __NAMESPACE__ . '\\filter_nav_menu_after', 10, 2 );

	// Clear menu caches when a nav menu is updated.
	add_action( 'wp_update_nav_menu', __NAMESPACE__ . '\\flush_menu_caches' );
}

/**
 * Get a cached version of a menu.
 *
 * @param string $key Menu cache key.
 *
 * @return string|array|false
 */
function get_cached_menu( string $key ) : string|array|false {
	return wp_cache_get( get_menu_cache_key( $key ), CACHE_KEY );
}

/**
 * Cache menu using a key.
 *
 * @param string $key Menu cache key.
 * @param string|array $content Menu contents or array of items to cache.
 * @return boolean
 */
function set_cached_menu( string $key, $content ) : bool {
	return wp_cache_set( get_menu_cache_key( $key ), $content, CACHE_KEY );
}

/**
 * Get a stringfied cache key based on menu id and request path.
 *
 * @param string $key Menu cache key.
 *
 * @return void
 */
function get_menu_cache_key( string $key ) {
	$page_id = is_404() ? '404' : crc32( $GLOBALS['wp']->request );
	return sprintf( '%s-%s', $key, $page_id );
}

/**
 * Flush menu caches once a menu is updated.
 *
 * @return void
 */
function flush_menu_caches() : void {
	wp_cache_delete_group( CACHE_KEY );
}

/**
 * Filter WP Nav menu pre, to return cached menu if available.
 *
 * @param string|null $menu
 * @param object $args
 * @return string|null
 */
function filter_nav_menu_before( ?string $menu, object $args ) : ?string {
	if ( ! empty( $args->theme_location ) ) {
		$key = $args->theme_location;
	} elseif ( ! empty( $args->menu ) ) {
		$key = ( $args->menu instanceof WP_Term ) ? $args->menu->name : $args->menu;
	}

	if ( empty( $key ) ) {
		return $menu;
	}

	$cache = get_cached_menu( $key );
	if ( ! empty( $cache ) ) {
		return $cache;
	}

	return $menu;
}

/**
 * Filter WP Nav menu - post, to store rendered menu in cache.
 *
 * @param string|null $menu
 * @param object $args
 * @return string|null
 */
function filter_nav_menu_after( string $menu, object $args ) : string {
	if ( ! empty( $args->theme_location ) ) {
		$key = $args->theme_location;
	} elseif ( ! empty( $args->menu ) ) {
		$key = ( $args->menu instanceof WP_Term ) ? $args->menu->name : $args->menu;
	}

	if ( ! empty( $key ) ) {
		set_cached_menu( $key, $menu );
	}

	return $menu;
}
