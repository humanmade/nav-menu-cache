<?php

namespace HM\Nav_Menu_Cache;

const CACHE_KEY = 'hm-nav-menu-cache';

/**
 * Init.
 *
 * @return void
 */
function bootstrap() {
	wp_cache_add_redis_hash_groups( CACHE_KEY );

	// Clear menu caches when a nav menu is updated.
	add_action( 'wp_update_nav_menu', __NAMESPACE__ . '\\flush_menu_caches' );

	// Clear menu caches when post or term is updated.
	add_action( 'post_updated', __NAMESPACE__ . '\\post_updated' );
	add_action( 'term_updated', __NAMESPACE__ . '\\term_updated' );
	add_action( 'hm_nav_menu_cache_clear_cache_for_post', __NAMESPACE__ . '\\clear_menu_cache_for_post' );
	add_action( 'hm_nav_menu_cache_clear_cache_for_term', __NAMESPACE__ . '\\clear_menu_cache_for_term' );
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
 * Handle clearing cache when a post is updated.
 *
 * This functionality is hooked in on a cron job,
 * in case there are a lot of nav menus, or items.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function clear_menu_cache_for_post( int $post_id ) : void {
	$nav_menus = wp_get_nav_menus();
	foreach ( $nav_menus as $nav_menu ) {
		$items = wp_get_nav_menu_items( $nav_menu );

		foreach ( $items as $item ) {
			if ( $item->type === 'post_type' && (int) $post_id === (int) $item->object_id ) {
				flush_menu_caches();
			}
		}
	}
}

/**
 * Handle clearing cache when a term is updated.
 *
 * This functionality is hooked in on a cron job,
 * in case there are a lot of nav menus, or items.
 *
 * @param int $term_id Term ID.
 * @return void
 */
function clear_menu_cache_for_term( int $term_id ) : void {
	$nav_menus = wp_get_nav_menus();
	foreach ( $nav_menus as $nav_menu ) {
		$items = wp_get_nav_menu_items( $nav_menu );
		foreach ( $items as $item ) {
			if ( $item->type === 'taxonomy' && $term_id === $item->object_id ) {
				flush_menu_caches();
			}
		}
	}
}
