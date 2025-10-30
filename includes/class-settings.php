<?php
/**
 * Settings helper for BT Site Recommendations
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Settings {
    
    /**
     * Get all plugin settings
     */
    public static function get_all() {
        return array(
            'permissions' => self::get_permissions(),
            'api_keys' => self::get_api_keys(),
            'hosting' => self::get_hosting_config(),
            'ai_provider' => self::get_default_ai_provider(),
        );
    }
    
    /**
     * Get permissions settings
     */
    public static function get_permissions() {
        $defaults = array(
            'read_theme_files' => true,
            'read_plugin_files' => true,
            'read_wp_config' => true,
            'access_db_structure' => true,
            'query_db_content' => true,
            'read_image_metadata' => true,
            'access_image_files' => true,
        );
        
        $permissions = get_option('bt_site_recommendations_permissions', $defaults);
        return wp_parse_args($permissions, $defaults);
    }
    
    /**
     * Update permissions
     */
    public static function update_permissions($permissions) {
        return update_option('bt_site_recommendations_permissions', $permissions);
    }
    
    /**
     * Check if a specific permission is granted
     */
    public static function has_permission($permission) {
        $permissions = self::get_permissions();
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
    
    /**
     * Get API keys (never expose in responses)
     */
    public static function get_api_keys() {
        return array(
            'openai' => get_option('bt_site_recommendations_openai_api_key', ''),
            'anthropic' => get_option('bt_site_recommendations_anthropic_api_key', ''),
        );
    }
    
    /**
     * Check if API key is set for a provider
     */
    public static function has_api_key($provider) {
        $keys = self::get_api_keys();
        return !empty($keys[$provider]);
    }
    
    /**
     * Update API key for a provider
     */
    public static function update_api_key($provider, $key) {
        $option_name = 'bt_site_recommendations_' . $provider . '_api_key';
        return update_option($option_name, sanitize_text_field($key));
    }
    
    /**
     * Get hosting configuration
     */
    public static function get_hosting_config() {
        return array(
            'type' => get_option('bt_site_recommendations_hosting_type', 'auto'),
            'override' => get_option('bt_site_recommendations_hosting_override', ''),
            'db_credentials' => get_option('bt_site_recommendations_db_credentials', array()),
        );
    }
    
    /**
     * Update hosting configuration
     */
    public static function update_hosting_config($config) {
        if (isset($config['type'])) {
            update_option('bt_site_recommendations_hosting_type', $config['type']);
        }
        if (isset($config['override'])) {
            update_option('bt_site_recommendations_hosting_override', $config['override']);
        }
        if (isset($config['db_credentials'])) {
            update_option('bt_site_recommendations_db_credentials', $config['db_credentials']);
        }
        return true;
    }
    
    /**
     * Get default AI provider
     */
    public static function get_default_ai_provider() {
        $default = get_option('bt_site_recommendations_default_ai_provider', '');
        
        // If not set, choose based on available API keys
        if (empty($default)) {
            if (self::has_api_key('openai')) {
                $default = 'openai';
            } elseif (self::has_api_key('anthropic')) {
                $default = 'anthropic';
            }
        }
        
        return $default;
    }
    
    /**
     * Update default AI provider
     */
    public static function update_default_ai_provider($provider) {
        if (in_array($provider, array('openai', 'anthropic'))) {
            return update_option('bt_site_recommendations_default_ai_provider', $provider);
        }
        return false;
    }
    
    /**
     * Get cache expiry time in seconds
     */
    public static function get_cache_expiry() {
        return get_option('bt_site_recommendations_cache_expiry', 24 * HOUR_IN_SECONDS);
    }
    
    /**
     * Clear all cached analysis results
     */
    public static function clear_cache() {
        delete_transient('bt_site_recommendations_code_analysis');
        delete_transient('bt_site_recommendations_db_analysis');
        delete_transient('bt_site_recommendations_image_analysis');
        return true;
    }
}

