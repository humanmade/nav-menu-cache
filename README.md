WP Nav Menu Cache
=================

Very large nav menus can impact performance of page load as they perform a significant number of calls to the object cache. However typically menus don't change frequently and are a good candidate to be cached. Caching menus isn't straight forwards of course, as we need to highlight the current page.

The WP Nav Menu cache plugin allows you too cache a nav menu, storing the rendered nav menu in cache for each unique request path.

A large mega-nav with around 40 menu items introduces around 100 cache get requests, that can be avoided when using this plugin.

Designed for use with [WP Redis](https://github.com/humanmade/wp-redis) object caching as it makes use of `wp_cache_delete_group` and `wp_cache_add_redis_hash_groups`.

Technical Details. 
==================

Generated menu HTML is cached under a key that is based on `$GLOBALS['wp']->request`. This means that there is one cache entry for every unique request. This ensures that differences in the generated markup for highlighting current pages etc is always correct.

The exception to this is when `is_404` is true, in which case a `404` key is used. This prevents unlimited cache entries for any random request. 

