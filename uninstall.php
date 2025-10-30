<?php
/**
 * Uninstall script for BT Site Recommendations
 * 
 * This file is executed when the plugin is uninstalled via the WordPress admin.
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('bt_site_recommendations_version');
delete_option('bt_site_recommendations_permissions');
delete_option('bt_site_recommendations_hosting_type');
delete_option('bt_site_recommendations_hosting_override');
delete_option('bt_site_recommendations_cache_expiry');
delete_option('bt_site_recommendations_openai_api_key');
delete_option('bt_site_recommendations_anthropic_api_key');
delete_option('bt_site_recommendations_default_ai_provider');
delete_option('bt_site_recommendations_db_credentials');

// Delete cached analysis results
delete_transient('bt_site_recommendations_code_analysis');
delete_transient('bt_site_recommendations_db_analysis');
delete_transient('bt_site_recommendations_image_analysis');

// Delete any custom database tables if created
global $wpdb;
// Currently not using custom tables, but prepared for future use
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}bt_site_recommendations_reports");

