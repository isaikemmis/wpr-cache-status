=== WPR Cache Status ===
Contributors: isaikemmis
Tags: wp rocket, cache, caching, rucss, remove unused css, performance, optimization, admin, dashboard
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display cache + RUCSS (Remove Unused CSS) status for your content directly inside the WP Rocket dashboard.

== Description ==

WPR Cache Status adds a “Cache Status” section to the WP Rocket settings page, giving you a quick, visual overview of:

* Which published URLs are cached
* RUCSS progress/state for URLs (when RUCSS is enabled in WP Rocket)
* Coverage statistics (cache + RUCSS)
* Search, filtering, and pagination for large sites

This plugin is **admin-only** (no frontend output) and is designed to help you spot pages that aren’t cached yet or where RUCSS is still processing/failed.

**Requirements**
* WP Rocket (required)
* WordPress 5.0+
* PHP 7.4+

== Installation ==

= From the WordPress admin =
1. Upload the plugin ZIP via **Plugins → Add New → Upload Plugin**.
2. Activate **WPR Cache Status**.
3. Go to **Settings → WP Rocket** to find the “Cache Status” section.

= Manual =
1. Upload the `wpr-cache-status` folder to `/wp-content/plugins/`.
2. Activate the plugin in **Plugins**.
3. Open **Settings → WP Rocket**.

== Changelog ==

= 1.0.0 =
* Initial release
* Cache status monitoring
* RUCSS status tracking (when enabled)
* Coverage statistics
* Search and pagination
* Responsive UI

== Upgrade Notice ==

= 1.0.0 =
Initial release.
