<?php
/**
 * Plugin Name: BT Site Recommendations
 * Plugin URI: https://github.com/Bartek55/bt_site_recommendations
 * Description: AI-powered WordPress plugin that analyzes your site to provide Page Speed and SEO optimization recommendations.
 * Version: 1.0.0
 * Author: Bartek55
 * Author URI: https://github.com/Bartek55
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: bt-site-recommendations
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('BT_SITE_RECOMMENDATIONS_VERSION', '1.0.0');

// Plugin directory path
define('BT_SITE_RECOMMENDATIONS_PATH', plugin_dir_path(__FILE__));

// Plugin directory URL
define('BT_SITE_RECOMMENDATIONS_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_bt_site_recommendations() {
    require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-activator.php';
    BT_Site_Recommendations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_bt_site_recommendations() {
    require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-deactivator.php';
    BT_Site_Recommendations_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_bt_site_recommendations');
register_deactivation_hook(__FILE__, 'deactivate_bt_site_recommendations');

/**
 * The core plugin class.
 */
require BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations.php';

/**
 * Begins execution of the plugin.
 */
function run_bt_site_recommendations() {
    $plugin = new BT_Site_Recommendations();
    $plugin->run();
}
run_bt_site_recommendations();
