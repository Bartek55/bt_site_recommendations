<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations_Admin {

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style('bt-site-recommendations-admin', BT_SITE_RECOMMENDATIONS_URL . 'assets/css/admin.css', array(), BT_SITE_RECOMMENDATIONS_VERSION, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script('bt-site-recommendations-admin', BT_SITE_RECOMMENDATIONS_URL . 'assets/js/admin.js', array('jquery'), BT_SITE_RECOMMENDATIONS_VERSION, false);
        wp_localize_script('bt-site-recommendations-admin', 'btSiteRecommendations', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bt_site_recommendations_nonce')
        ));
    }

    /**
     * Register the administration menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('Site Recommendations', 'bt-site-recommendations'),
            __('Site Recommendations', 'bt-site-recommendations'),
            'manage_options',
            'bt-site-recommendations',
            array($this, 'display_plugin_admin_page'),
            'dashicons-chart-area',
            80
        );

        add_submenu_page(
            'bt-site-recommendations',
            __('Settings', 'bt-site-recommendations'),
            __('Settings', 'bt-site-recommendations'),
            'manage_options',
            'bt-site-recommendations-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting('bt_site_recommendations_settings', 'bt_site_recommendations_settings');
    }

    /**
     * Render the main admin page.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/views/admin-display.php';
    }

    /**
     * Render the settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/views/admin-settings.php';
    }

    /**
     * AJAX handler for site analysis.
     *
     * @since    1.0.0
     */
    public function ajax_analyze_site() {
        check_ajax_referer('bt_site_recommendations_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'bt-site-recommendations')));
            return;
        }

        $analyzer = new BT_Site_Recommendations_Analyzer();
        $ai = new BT_Site_Recommendations_AI();

        // Analyze the site
        $page_speed_data = $analyzer->analyze_page_speed(home_url());
        $seo_data = $analyzer->analyze_seo();

        // Get AI recommendations
        $recommendations = $ai->generate_recommendations($page_speed_data, $seo_data);

        wp_send_json_success(array(
            'page_speed' => $page_speed_data,
            'seo' => $seo_data,
            'recommendations' => $recommendations
        ));
    }
}
