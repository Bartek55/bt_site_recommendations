<?php
/**
 * Plugin Name: BT Site Recommendations
 * Plugin URI: https://github.com/Bartek55/bt_site_recommendations
 * Description: AI-powered site analysis using GPT-5 or Claude Sonnet 4.5 to provide code, database, and image optimization recommendations.
 * Version: 1.0.0
 * Author: Bartek Trociuk
 * License: GPL v2 or later
 * Text Domain: bt-site-recommendations
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check PHP version compatibility
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>BT Site Recommendations:</strong> ';
        echo 'This plugin requires PHP 7.4 or higher. You are running PHP ' . PHP_VERSION . '. ';
        echo 'Please update your PHP version to use this plugin.';
        echo '</p></div>';
    });
    return;
}

// Define plugin constants
define('BT_SITE_RECOMMENDATIONS_VERSION', '1.0.0');
define('BT_SITE_RECOMMENDATIONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BT_SITE_RECOMMENDATIONS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BT_SITE_RECOMMENDATIONS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-settings.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-ai-provider-manager.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-hosting-detector.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-code-analyzer.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-database-analyzer.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-image-analyzer.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-image-optimizer.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'includes/class-bt-site-recommendations.php';
require_once BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'admin/class-admin-page.php';

// Initialize the plugin
function bt_site_recommendations_init() {
    new BT_Site_Recommendations();
}
add_action('plugins_loaded', 'bt_site_recommendations_init');

// Activation hook
register_activation_hook(__FILE__, 'bt_site_recommendations_activate');
function bt_site_recommendations_activate() {
    // Create necessary options with defaults
    add_option('bt_site_recommendations_version', BT_SITE_RECOMMENDATIONS_VERSION);
    
    // Set default permissions (all enabled by default)
    $default_permissions = array(
        'read_theme_files' => true,
        'read_plugin_files' => true,
        'read_wp_config' => true,
        'access_db_structure' => true,
        'query_db_content' => true,
        'read_image_metadata' => true,
        'access_image_files' => true,
    );
    add_option('bt_site_recommendations_permissions', $default_permissions);
    
    // Set default hosting type to 'auto'
    add_option('bt_site_recommendations_hosting_type', 'auto');
    
    // Initialize cache expiry option
    add_option('bt_site_recommendations_cache_expiry', 24 * HOUR_IN_SECONDS);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'bt_site_recommendations_deactivate');
function bt_site_recommendations_deactivate() {
    // Clean up transients
    delete_transient('bt_site_recommendations_code_analysis');
    delete_transient('bt_site_recommendations_db_analysis');
    delete_transient('bt_site_recommendations_image_analysis');
}

