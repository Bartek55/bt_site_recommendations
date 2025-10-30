<?php
/**
 * Main plugin class for BT Site Recommendations
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations {
    
    public function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_bt_sr_test_ai_connection', array($this, 'ajax_test_ai_connection'));
        add_action('wp_ajax_bt_sr_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_bt_sr_analyze_code', array($this, 'ajax_analyze_code'));
        add_action('wp_ajax_bt_sr_analyze_database', array($this, 'ajax_analyze_database'));
        add_action('wp_ajax_bt_sr_analyze_images', array($this, 'ajax_analyze_images'));
        add_action('wp_ajax_bt_sr_optimize_images', array($this, 'ajax_optimize_images'));
        add_action('wp_ajax_bt_sr_clear_cache', array($this, 'ajax_clear_cache'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('bt-site-recommendations', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Site Recommendations', 'bt-site-recommendations'),
            __('Site Recommendations', 'bt-site-recommendations'),
            'manage_options',
            'bt-site-recommendations',
            array($this, 'admin_page'),
            'dashicons-analytics',
            30
        );
    }
    
    public function admin_page() {
        $admin_page = new BT_Site_Recommendations_Admin_Page();
        $admin_page->render();
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bt-site-recommendations') === false) {
            return;
        }
        
        wp_enqueue_style(
            'bt-site-recommendations-admin',
            BT_SITE_RECOMMENDATIONS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BT_SITE_RECOMMENDATIONS_VERSION
        );
        
        wp_enqueue_script(
            'bt-site-recommendations-admin',
            BT_SITE_RECOMMENDATIONS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            BT_SITE_RECOMMENDATIONS_VERSION,
            true
        );
        
        wp_localize_script('bt-site-recommendations-admin', 'btSiteRecommendations', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bt_site_recommendations_nonce'),
            'strings' => array(
                'analyzing' => __('Analyzing...', 'bt-site-recommendations'),
                'error' => __('An error occurred. Please try again.', 'bt-site-recommendations'),
                'success' => __('Analysis complete!', 'bt-site-recommendations'),
                'testing' => __('Testing connection...', 'bt-site-recommendations'),
                'connected' => __('Connection successful!', 'bt-site-recommendations'),
                'connectionFailed' => __('Connection failed. Please check your API key.', 'bt-site-recommendations'),
                'saving' => __('Saving...', 'bt-site-recommendations'),
                'saved' => __('Settings saved!', 'bt-site-recommendations'),
                'optimizing' => __('Optimizing images...', 'bt-site-recommendations'),
                'optimized' => __('Images optimized successfully!', 'bt-site-recommendations'),
            )
        ));
    }
    
    public function ajax_test_ai_connection() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        $provider = isset($_POST['provider']) ? sanitize_text_field($_POST['provider']) : '';
        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($provider) || empty($api_key)) {
            wp_send_json_error(array('message' => __('Provider and API key are required.', 'bt-site-recommendations')));
        }
        
        $ai_manager = new BT_Site_Recommendations_AI_Provider_Manager();
        $result = $ai_manager->test_connection($provider, $api_key);
        
        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_save_settings() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        
        // Save permissions
        if (isset($settings['permissions'])) {
            BT_Site_Recommendations_Settings::update_permissions($settings['permissions']);
        }
        
        // Save API keys
        if (isset($settings['openai_api_key'])) {
            BT_Site_Recommendations_Settings::update_api_key('openai', $settings['openai_api_key']);
        }
        if (isset($settings['anthropic_api_key'])) {
            BT_Site_Recommendations_Settings::update_api_key('anthropic', $settings['anthropic_api_key']);
        }
        
        // Save default AI provider
        if (isset($settings['default_ai_provider'])) {
            BT_Site_Recommendations_Settings::update_default_ai_provider($settings['default_ai_provider']);
        }
        
        // Save hosting configuration
        if (isset($settings['hosting'])) {
            BT_Site_Recommendations_Settings::update_hosting_config($settings['hosting']);
        }
        
        wp_send_json_success(array('message' => __('Settings saved successfully.', 'bt-site-recommendations')));
    }
    
    public function ajax_analyze_code() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        // Check cache first
        $cached = get_transient('bt_site_recommendations_code_analysis');
        $force = isset($_POST['force']) && $_POST['force'] === 'true';
        
        if ($cached && !$force) {
            wp_send_json_success(array(
                'data' => $cached,
                'cached' => true,
                'message' => __('Analysis retrieved from cache.', 'bt-site-recommendations')
            ));
        }
        
        $analyzer = new BT_Site_Recommendations_Code_Analyzer();
        $result = $analyzer->analyze();
        
        if ($result['success']) {
            // Cache the results
            set_transient('bt_site_recommendations_code_analysis', $result['data'], BT_Site_Recommendations_Settings::get_cache_expiry());
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_analyze_database() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        // Check cache first
        $cached = get_transient('bt_site_recommendations_db_analysis');
        $force = isset($_POST['force']) && $_POST['force'] === 'true';
        
        if ($cached && !$force) {
            wp_send_json_success(array(
                'data' => $cached,
                'cached' => true,
                'message' => __('Analysis retrieved from cache.', 'bt-site-recommendations')
            ));
        }
        
        $analyzer = new BT_Site_Recommendations_Database_Analyzer();
        $result = $analyzer->analyze();
        
        if ($result['success']) {
            // Cache the results
            set_transient('bt_site_recommendations_db_analysis', $result['data'], BT_Site_Recommendations_Settings::get_cache_expiry());
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_analyze_images() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        // Check cache first
        $cached = get_transient('bt_site_recommendations_image_analysis');
        $force = isset($_POST['force']) && $_POST['force'] === 'true';
        
        if ($cached && !$force) {
            wp_send_json_success(array(
                'data' => $cached,
                'cached' => true,
                'message' => __('Analysis retrieved from cache.', 'bt-site-recommendations')
            ));
        }
        
        $analyzer = new BT_Site_Recommendations_Image_Analyzer();
        $result = $analyzer->analyze();
        
        if ($result['success']) {
            // Cache the results
            set_transient('bt_site_recommendations_image_analysis', $result['data'], BT_Site_Recommendations_Settings::get_cache_expiry());
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_optimize_images() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        
        if (empty($image_ids) || empty($action)) {
            wp_send_json_error(array('message' => __('Invalid request.', 'bt-site-recommendations')));
        }
        
        $optimizer = new BT_Site_Recommendations_Image_Optimizer();
        $result = $optimizer->optimize_batch($image_ids, $action);
        
        if ($result['success']) {
            // Clear image analysis cache
            delete_transient('bt_site_recommendations_image_analysis');
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_clear_cache() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bt-site-recommendations')));
        }
        
        BT_Site_Recommendations_Settings::clear_cache();
        wp_send_json_success(array('message' => __('Cache cleared successfully.', 'bt-site-recommendations')));
    }
}

