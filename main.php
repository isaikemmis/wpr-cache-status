<?php
/**
 * Plugin Name: WPR Cache Status
 * Plugin URI: https://github.com/isaikemmis/wpr-cache-status
 * Description: Display cache and RUCSS status for all pages in WP Rocket dashboard
 * Version: 1.0
 * Author: Isai Kemmis
 * Author URI: https://github.com/isaikemmis/
 * Text Domain: wpr-cache-status
 * License: GPL-2.0+
 */

defined('ABSPATH') || exit;

/// Plugin file
define('WPR_CACHE_STATUS_PLUGIN_FILE', __FILE__);

/// Plugin directory
define('WPR_CACHE_STATUS_PLUGIN_DIR', plugin_dir_path(__FILE__));

/// Plugin URL
define('WPR_CACHE_STATUS_PLUGIN_URL', plugin_dir_url(__FILE__));

/// Plugin slug
define('WPR_CACHE_STATUS_PLUGIN_SLUG', basename(__DIR__) . '/' . basename(__FILE__));

/// Initialize classes
add_action('plugins_loaded', array('WPR_Cache_Status', 'init'), 20);

/// Simple autoload
spl_autoload_register(function($class_name){
      /// Get class name
      preg_match('~^WPR_Cache_Status_(.*)~', $class_name, $matches);
      if (isset($matches[1]) && !empty($matches[1])){
            $filename = str_replace(array('.','_'), array('','-'), strtolower($matches[1]));
            require_once WPR_CACHE_STATUS_PLUGIN_DIR . 'classes/'.$filename.'.class.php';
      }
});

function wpr_cache_status_get_version(){
      static $version = null;
      if ($version === null){
            $plugin_data = get_plugin_data(WPR_CACHE_STATUS_PLUGIN_FILE);
            $version = $plugin_data['Version'];
      }
      return $version;
}

class WPR_Cache_Status {

      /// Initialize plugin
      public static function init(){
            /// Check if WP Rocket is active
            if (!defined('WP_ROCKET_VERSION')){
                  add_action('admin_notices', array(__CLASS__, 'wp_rocket_required_notice'));
                  return;
            }

            if (is_admin()){
                  /// Initialize Dashboard
                  WPR_Cache_Status_Dashboard::init();

                  /// Initialize AJAX handler
                  WPR_Cache_Status_Ajax::init();
            }
      }

      /// WP Rocket required notice
      public static function wp_rocket_required_notice(){
            ?>
            <div class="notice notice-error">
                  <p><?php esc_html_e('WPR Cache Status requires WP Rocket to be installed and activated.', 'wpr-cache-status'); ?></p>
            </div>
            <?php
      }
}
