<?php

class WPR_Cache_Status_Dashboard {

      /// Initialize dashboard
      public static function init(){
            /// Add to WP Rocket dashboard after account data
            add_action('rocket_dashboard_after_account_data', array(__CLASS__, 'render_dashboard_section'));

            /// Enqueue admin assets
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
      }

      /// Enqueue admin assets
      public static function enqueue_assets($hook){
            /// Only load on WP Rocket settings page
            if ($hook !== 'settings_page_wprocket'){
                  return;
            }

            $css_file = WPR_CACHE_STATUS_PLUGIN_DIR . 'assets/css/admin.css';
            $js_file = WPR_CACHE_STATUS_PLUGIN_DIR . 'assets/js/admin.js';

            wp_enqueue_style(
                  'wpr-cache-status',
                  WPR_CACHE_STATUS_PLUGIN_URL . 'assets/css/admin.css',
                  array(),
                  file_exists($css_file) ? md5_file($css_file) : wpr_cache_status_get_version()
            );

            wp_enqueue_script(
                  'wpr-cache-status',
                  WPR_CACHE_STATUS_PLUGIN_URL . 'assets/js/admin.js',
                  array('jquery'),
                  file_exists($js_file) ? md5_file($js_file) : wpr_cache_status_get_version(),
                  true
            );

            wp_localize_script('wpr-cache-status', 'wprCacheStatus', array(
                  'ajaxUrl' => admin_url('admin-ajax.php'),
                  'nonce' => wp_create_nonce('rocket_warmup_table_nonce'),
                  'strings' => array(
                        'loading' => __('Loading cache status...', 'wpr-cache-status'),
                        'error' => __('Error loading cache status', 'wpr-cache-status'),
                        'cached' => __('Cached', 'wpr-cache-status'),
                        'notCached' => __('Not Cached', 'wpr-cache-status'),
                        'rucssColumn' => __('RUCSS Status', 'wpr-cache-status'),
                        'rucssComplete' => __('Complete', 'wpr-cache-status'),
                        'rucssProcessing' => __('Processing', 'wpr-cache-status'),
                        'rucssNotStarted' => __('Not Started', 'wpr-cache-status'),
                        'rucssDisabled' => __('Disabled', 'wpr-cache-status'),
                        'rucssFailed' => __('Failed', 'wpr-cache-status'),
                  )
            ));
      }

      /// Render dashboard section
      public static function render_dashboard_section(){
            /// Check if RUCSS is enabled
            $rucss_enabled = function_exists('get_rocket_option') && get_rocket_option('remove_unused_css', false);
            ?>
            <div class="wpr-optionHeader" style="margin-top: 30px;">
                  <h3 class="wpr-title2"><?php esc_html_e('Cache Status', 'wpr-cache-status'); ?></h3>
            </div>
            
            <div class="wpr-fieldsContainer-fieldset">
                  <div class="wpr-field rocket-warmup-loading-overlay" id="rocket-warmup-content">
                        <div style="margin-bottom: 20px;">
                              <button type="button" id="wpr-cache-status-refresh" class="wpr-button wpr-button--icon wpr-button--small wpr-icon-refresh">
                                    <?php esc_html_e('Refresh Status', 'wpr-cache-status'); ?>
                              </button>
                        </div>
                        
                        <div id="wpr-cache-status-stats" class="rocket-stats-container">
                              <div class="rocket-stat-box">
                                    <div class="rocket-stat-label"><?php esc_html_e('Cache Coverage', 'wpr-cache-status'); ?></div>
                                    <div class="rocket-stat-value">
                                          <span id="rocket-warmup-cache-percentage">...</span>%
                                    </div>
                                    <div class="rocket-stat-detail">
                                          <span id="rocket-warmup-cache-count">...</span> / <span id="rocket-warmup-total-count">...</span> <?php esc_html_e('pages', 'wpr-cache-status'); ?>
                                    </div>
                              </div>
                              <?php if ($rucss_enabled) : ?>
                              <div id="rocket-warmup-rucss-stats" class="rocket-stat-box">
                                    <div class="rocket-stat-label"><?php esc_html_e('RUCSS Coverage', 'wpr-cache-status'); ?></div>
                                    <div class="rocket-stat-value">
                                          <span id="rocket-warmup-rucss-percentage">...</span>%
                                    </div>
                                    <div class="rocket-stat-detail">
                                          <span id="rocket-warmup-rucss-count">...</span> / <span id="rocket-warmup-total-count-rucss">...</span> <?php esc_html_e('pages', 'wpr-cache-status'); ?>
                                    </div>
                              </div>
                              <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 15px; margin-top: 20px; align-items: center; justify-content: space-between;">
                              
                              <div style="display: flex; align-items: center; gap: 10px;">
                                    <span id="wpr-cache-status-info"></span>
                                    <select id="wpr-cache-status-per-page" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                          <option value="10">10 <?php esc_html_e('per page', 'wpr-cache-status'); ?></option>
                                          <option value="25" selected>25 <?php esc_html_e('per page', 'wpr-cache-status'); ?></option>
                                          <option value="50">50 <?php esc_html_e('per page', 'wpr-cache-status'); ?></option>
                                          <option value="100">100 <?php esc_html_e('per page', 'wpr-cache-status'); ?></option>
                                    </select>
                              </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                              <input type="text" id="wpr-cache-status-search" placeholder="<?php esc_attr_e('Search URLs...', 'wpr-cache-status'); ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 400px;">
                        </div>

                        <div class="wpr-cache-status-wrapper">
                              <table class="wpr-cache-status widefat">
                                    <thead>
                                          <tr>
                                                <th><?php esc_html_e('URL', 'wpr-cache-status'); ?></th>
                                                <th><?php esc_html_e('Cache', 'wpr-cache-status'); ?></th>
                                                <?php if ($rucss_enabled) : ?>
                                                <th><?php esc_html_e('RUCSS', 'wpr-cache-status'); ?></th>
                                                <?php endif; ?>
                                                <th><?php esc_html_e('Last Modified', 'wpr-cache-status'); ?></th>
                                          </tr>
                                    </thead>
                                    <tbody id="wpr-cache-status-body">
                                          <tr>
                                                <td colspan="<?php echo $rucss_enabled ? '4' : '3'; ?>" class="wpr-cache-status-loading">
                                                      <?php esc_html_e('Loading...', 'wpr-cache-status'); ?>
                                                </td>
                                          </tr>
                                    </tbody>
                              </table>
                              
                              <div id="wpr-cache-status-pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 10px 0;">
                                    <div id="wpr-cache-status-showing"></div>
                                    <div id="wpr-cache-status-nav" style="display: flex; gap: 5px;"></div>
                              </div>
                        </div>
                  </div>
            </div>
            <?php
      }
}
