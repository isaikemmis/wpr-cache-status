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
                        'loading' => __('Loading cache status...', 'rocket-warmup-table'),
                        'error' => __('Error loading cache status', 'rocket-warmup-table'),
                        'cached' => __('Cached', 'rocket-warmup-table'),
                        'notCached' => __('Not Cached', 'rocket-warmup-table'),
                        'rucssColumn' => __('RUCSS Status', 'rocket-warmup-table'),
                        'rucssComplete' => __('Complete', 'rocket-warmup-table'),
                        'rucssProcessing' => __('Processing', 'rocket-warmup-table'),
                        'rucssNotStarted' => __('Not Started', 'rocket-warmup-table'),
                        'rucssDisabled' => __('Disabled', 'rocket-warmup-table'),
                        'rucssFailed' => __('Failed', 'rocket-warmup-table'),
                  )
            ));
      }

      /// Render dashboard section
      public static function render_dashboard_section(){
            /// Check if RUCSS is enabled
            $rucss_enabled = function_exists('get_rocket_option') && get_rocket_option('remove_unused_css', false);
            ?>
            <div class="wpr-optionHeader" style="margin-top: 30px;">
                  <h3 class="wpr-title2"><?php esc_html_e('Cache Status', 'rocket-warmup-table'); ?></h3>
            </div>
            
            <div class="wpr-fieldsContainer-fieldset">
                  <div class="wpr-field rocket-warmup-loading-overlay" id="rocket-warmup-content">
                        <div style="margin-bottom: 20px;">
                              <button type="button" id="rocket-warmup-table-refresh" class="wpr-button wpr-button--icon wpr-button--small wpr-icon-refresh">
                                    <?php esc_html_e('Refresh Status', 'rocket-warmup-table'); ?>
                              </button>
                        </div>
                        
                        <div id="rocket-warmup-table-stats" class="rocket-stats-container">
                              <div class="rocket-stat-box">
                                    <div class="rocket-stat-label"><?php esc_html_e('Cache Coverage', 'rocket-warmup-table'); ?></div>
                                    <div class="rocket-stat-value">
                                          <span id="rocket-warmup-cache-percentage">...</span>%
                                    </div>
                                    <div class="rocket-stat-detail">
                                          <span id="rocket-warmup-cache-count">...</span> / <span id="rocket-warmup-total-count">...</span> <?php esc_html_e('pages', 'rocket-warmup-table'); ?>
                                    </div>
                              </div>
                              <?php if ($rucss_enabled) : ?>
                              <div id="rocket-warmup-rucss-stats" class="rocket-stat-box">
                                    <div class="rocket-stat-label"><?php esc_html_e('RUCSS Coverage', 'rocket-warmup-table'); ?></div>
                                    <div class="rocket-stat-value">
                                          <span id="rocket-warmup-rucss-percentage">...</span>%
                                    </div>
                                    <div class="rocket-stat-detail">
                                          <span id="rocket-warmup-rucss-count">...</span> / <span id="rocket-warmup-total-count-rucss">...</span> <?php esc_html_e('pages', 'rocket-warmup-table'); ?>
                                    </div>
                              </div>
                              <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 15px; margin-top: 20px; align-items: center; justify-content: space-between;">
                              
                              <div style="display: flex; align-items: center; gap: 10px;">
                                    <span id="rocket-warmup-table-info"></span>
                                    <select id="rocket-warmup-table-per-page" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                          <option value="10">10 <?php esc_html_e('per page', 'rocket-warmup-table'); ?></option>
                                          <option value="25" selected>25 <?php esc_html_e('per page', 'rocket-warmup-table'); ?></option>
                                          <option value="50">50 <?php esc_html_e('per page', 'rocket-warmup-table'); ?></option>
                                          <option value="100">100 <?php esc_html_e('per page', 'rocket-warmup-table'); ?></option>
                                    </select>
                              </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                              <input type="text" id="rocket-warmup-table-search" placeholder="<?php esc_attr_e('Search URLs...', 'rocket-warmup-table'); ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 400px;">
                        </div>

                        <div class="rocket-warmup-table-wrapper">
                              <table class="rocket-warmup-table widefat">
                                    <thead>
                                          <tr>
                                                <th><?php esc_html_e('URL', 'rocket-warmup-table'); ?></th>
                                                <th><?php esc_html_e('Cache', 'rocket-warmup-table'); ?></th>
                                                <?php if ($rucss_enabled) : ?>
                                                <th><?php esc_html_e('RUCSS', 'rocket-warmup-table'); ?></th>
                                                <?php endif; ?>
                                                <th><?php esc_html_e('Last Modified', 'rocket-warmup-table'); ?></th>
                                          </tr>
                                    </thead>
                                    <tbody id="rocket-warmup-table-body">
                                          <tr>
                                                <td colspan="<?php echo $rucss_enabled ? '4' : '3'; ?>" class="rocket-warmup-table-loading">
                                                      <?php esc_html_e('Loading...', 'rocket-warmup-table'); ?>
                                                </td>
                                          </tr>
                                    </tbody>
                              </table>
                              
                              <div id="rocket-warmup-table-pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 10px 0;">
                                    <div id="rocket-warmup-table-showing"></div>
                                    <div id="rocket-warmup-table-nav" style="display: flex; gap: 5px;"></div>
                              </div>
                        </div>
                  </div>
            </div>
            <?php
      }
}
