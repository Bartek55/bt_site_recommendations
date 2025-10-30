<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations_Deactivator {

    /**
     * Deactivate the plugin.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Clean up scheduled tasks if any
        wp_clear_scheduled_hook('bt_site_recommendations_cron');
    }
}
