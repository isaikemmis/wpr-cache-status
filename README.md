# WPR Cache Status

A WordPress plugin that displays cache and RUCSS (Remove Unused CSS) status for all pages directly in the WP Rocket dashboard.

## Description

WPR Cache Status provides a comprehensive overview of your website's cache and RUCSS optimization status. Monitor which pages are cached, track RUCSS completion, and identify optimization opportunities—all from a beautiful interface integrated into your WP Rocket dashboard.

## Features

- **Real-time Cache Status** - View cache status for all published pages and posts
- **RUCSS Monitoring** - Track Remove Unused CSS optimization progress
- **Coverage Statistics** - See cache and RUCSS coverage percentages at a glance
- **Search & Filter** - Quickly find specific URLs with the built-in search
- **Pagination** - Handle large sites with efficient client-side pagination
- **Responsive Design** - Beautiful gradient stat boxes that adapt to mobile devices
- **Database-Driven** - Queries WP Rocket's database tables directly for accurate data

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- **WP Rocket plugin** (required)

## Installation

1. Download the plugin files
2. Upload the `wpr-cache-status` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Settings > WP Rocket** to view the Cache Status section

## Usage

After activation, the plugin automatically adds a "Cache Status" section to your WP Rocket dashboard page.

### Cache Coverage Stats

View two prominent stat boxes showing:
- **Cache Coverage** - Percentage of pages that are cached
- **RUCSS Coverage** - Percentage of pages with completed RUCSS optimization (if enabled)

### Status Table

The table displays:
- **URL** - The page URL
- **Cache** - Cache status (Cached / Not Cached)
- **RUCSS** - RUCSS status (Complete / Processing / Failed / Not Started) - only shown if RUCSS is enabled
- **Last Modified** - When the cache was last updated

### Controls

- **Refresh Status** - Reload data from the database
- **Search** - Filter URLs by typing in the search box
- **Per Page** - Choose how many rows to display (10, 25, 50, or 100)
- **Pagination** - Navigate through multiple pages of results

## Status Indicators

### Cache Status
- 🟢 **Cached** - Page is successfully cached
- 🔴 **Not Cached** - Page is not yet cached

### RUCSS Status
- 🟢 **Complete** - RUCSS optimization completed
- 🟡 **Processing** - RUCSS optimization in progress
- 🔴 **Failed** - RUCSS optimization failed
- ⚪ **Not Started** - RUCSS optimization not yet started

## Technical Details

### Database Tables

The plugin queries the following WP Rocket database tables:
- `wp_wpr_rocket_cache` - Cache status and timestamps
- `wp_wpr_rucss_used_css` - RUCSS optimization status

### Performance

- Client-side pagination for efficient handling of large datasets
- All URLs loaded into JavaScript but only visible rows rendered to DOM
- MD5 cache busting for CSS/JS assets
- Minimal server load with single database queries

## Frequently Asked Questions

**Q: Will this slow down my site?**  
A: No, the plugin only loads on the WP Rocket settings page in the admin area and has no frontend impact.

**Q: How often is the data updated?**  
A: Data is loaded from the database each time you view the page or click the Refresh button.


## Changelog

### 1.0
- Initial release
- Cache status monitoring
- RUCSS status tracking
- Coverage statistics
- Search and pagination
- Responsive design
- Loading overlay

## Support

For issues, questions, or feature requests, please visit:
- GitHub: [https://github.com/isaikemmis/wpr-cache-status](https://github.com/isaikemmis/wpr-cache-status)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by [Isai Kemmis](https://github.com/isaikemmis/)