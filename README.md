WP Nav Menu Cache
=================

Designed for use with [WP Redis](https://github.com/humanmade/wp-redis) object caching.

Very large nav menus can cause performance issues as they perform a significant number of calls to the object cache, which can impact performance. However typically these don't change frequently and are a good candidate to be cached. Caching menus isn't straight forwards of course, as we need to highlight the current page.

The WP Nav Menu cache plugin allows you too cache a nav menu, storing the rendered nav menu in cache for each unique request path.

A large mega-nav with around 40 menu items introduces around 100 cache get requests, that can be avoided when using this plugin.
