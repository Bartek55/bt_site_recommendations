<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations_Activator {

    /**
     * Activate the plugin.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Create default options
        if (!get_option('bt_site_recommendations_settings')) {
            add_option('bt_site_recommendations_settings', array(
                'analysis_frequency' => 'weekly',
                'enable_page_speed' => true,
                'enable_seo' => true,
                'ai_provider' => 'openai'
            ));
        }
    }
}
