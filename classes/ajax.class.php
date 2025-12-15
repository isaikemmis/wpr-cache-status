<?php

class WPR_Cache_Status_Ajax {

      /// Initialize AJAX handlers
      public static function init(){
            add_action('wp_ajax_rocket_warmup_table_get_status', array(__CLASS__, 'get_status'));
      }

      /// Get cache and RUCSS status for all URLs
      public static function get_status(){
            /// Verify nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rocket_warmup_table_nonce')){
                  wp_send_json_error(array('message' => __('Invalid nonce', 'rocket-warmup-table')));
            }

            if (!current_user_can('manage_options')){
                  wp_send_json_error(array('message' => __('Insufficient permissions', 'rocket-warmup-table')));
            }

            /// Check if RUCSS is enabled
            $rucss_enabled = function_exists('get_rocket_option') && get_rocket_option('remove_unused_css', false);

            global $wpdb;
            $cache_table = $wpdb->prefix . 'wpr_rocket_cache';
            $rucss_table = $wpdb->prefix . 'wpr_rucss_used_css';
            
            /// Get all cache data from database
            $cache_results = $wpdb->get_results(
                  "SELECT url, status, modified FROM {$cache_table} ORDER BY url, modified DESC"
            );
            
            /// Group by URL to get latest status
            $cache_data = array();
            foreach ($cache_results as $row) {
                  if (!isset($cache_data[$row->url])) {
                        $cache_data[$row->url] = array(
                              'status' => $row->status,
                              'modified' => $row->modified
                        );
                  }
            }
            
            /// Get RUCSS data if enabled
            $rucss_data = array();
            if ($rucss_enabled && $wpdb->get_var("SHOW TABLES LIKE '{$rucss_table}'") === $rucss_table) {
                  $rucss_results = $wpdb->get_results(
                        "SELECT url, status FROM {$rucss_table} ORDER BY url, last_accessed DESC"
                  );
                  
                  foreach ($rucss_results as $row) {
                        if (!isset($rucss_data[$row->url])) {
                              $rucss_data[$row->url] = $row->status;
                        }
                  }
            }
            
            /// Build status data
            $status_data = array();
            $cache_count = 0;
            $rucss_complete_count = 0;
            
            foreach ($cache_data as $url => $cache_info) {
                  $cache_status = ($cache_info['status'] === 'completed') ? 'cached' : 'not_cached';
                  
                  $item = array(
                        'url' => $url,
                        'cache_status' => $cache_status,
                        'last_modified' => $cache_info['modified'] ? 
                              human_time_diff(strtotime($cache_info['modified']), current_time('timestamp')) . ' ' . __('ago', 'rocket-warmup-table') : '-'
                  );
                  
                  if ($cache_status === 'cached') {
                        $cache_count++;
                  }
                  
                  if ($rucss_enabled) {
                        $rucss_status = 'not_started';
                        if (isset($rucss_data[$url])) {
                              $db_status = $rucss_data[$url];
                              
                              switch ($db_status) {
                                    case 'completed':
                                          $rucss_status = 'complete';
                                          $rucss_complete_count++;
                                          break;
                                    case 'pending':
                                    case 'in-progress':
                                          $rucss_status = 'processing';
                                          break;
                                    case 'failed':
                                          $rucss_status = 'failed';
                                          break;
                                    default:
                                          $rucss_status = 'not_started';
                              }
                        }
                        $item['rucss_status'] = $rucss_status;
                  }
                  
                  $status_data[] = $item;
            }

            $total_urls = count($status_data);
            $cache_percentage = $total_urls > 0 ? round(($cache_count / $total_urls) * 100, 1) : 0;
            $rucss_percentage = ($rucss_enabled && $total_urls > 0) ? round(($rucss_complete_count / $total_urls) * 100, 1) : 0;

            wp_send_json_success(array(
                  'items' => $status_data,
                  'rucss_enabled' => $rucss_enabled,
                  'stats' => array(
                        'total' => $total_urls,
                        'cached' => $cache_count,
                        'cache_percentage' => $cache_percentage,
                        'rucss_complete' => $rucss_complete_count,
                        'rucss_percentage' => $rucss_percentage
                  )
            ));
      }

}
