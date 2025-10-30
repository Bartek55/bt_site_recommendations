<?php
/**
 * Hosting Detector for BT Site Recommendations
 * Auto-detect Pantheon, WP Engine, or standard hosting
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Hosting_Detector {
    
    private $hosting_type = null;
    private $db_credentials = array();
    
    /**
     * Detect hosting environment
     */
    public function detect() {
        $override = get_option('bt_site_recommendations_hosting_override', '');
        
        // If manual override is set, use it
        if (!empty($override) && $override !== 'auto') {
            $this->hosting_type = $override;
        } else {
            // Auto-detect
            $this->hosting_type = $this->auto_detect_hosting();
        }
        
        // Get database credentials
        $this->db_credentials = $this->get_db_credentials();
        
        return array(
            'type' => $this->hosting_type,
            'db_credentials' => $this->db_credentials,
            'auto_detected' => empty($override) || $override === 'auto',
        );
    }
    
    /**
     * Auto-detect hosting type
     */
    private function auto_detect_hosting() {
        // Check for Pantheon
        if (defined('PANTHEON_ENVIRONMENT')) {
            return 'pantheon';
        }
        
        if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
            return 'pantheon';
        }
        
        if (getenv('PANTHEON_ENVIRONMENT') !== false) {
            return 'pantheon';
        }
        
        // Check for WP Engine
        if (defined('WPE_GOVERNOR')) {
            return 'wpengine';
        }
        
        if (function_exists('is_wpe') && is_wpe()) {
            return 'wpengine';
        }
        
        if (getenv('WPE_GOVERNOR') !== false) {
            return 'wpengine';
        }
        
        // Check for other common hosts
        if (defined('IS_PRESSABLE')) {
            return 'pressable';
        }
        
        if (defined('GD_SYSTEM_PLUGIN_DIR')) {
            return 'godaddy';
        }
        
        // Default to standard
        return 'standard';
    }
    
    /**
     * Get database credentials from wp-config.php or environment
     */
    private function get_db_credentials() {
        $credentials = array(
            'host' => '',
            'name' => '',
            'user' => '',
            'password' => '',
            'charset' => '',
            'collate' => '',
            'prefix' => '',
        );
        
        // Try to get from WordPress constants (most common)
        if (defined('DB_HOST')) {
            $credentials['host'] = DB_HOST;
        }
        
        if (defined('DB_NAME')) {
            $credentials['name'] = DB_NAME;
        }
        
        if (defined('DB_USER')) {
            $credentials['user'] = DB_USER;
        }
        
        if (defined('DB_PASSWORD')) {
            $credentials['password'] = DB_PASSWORD;
        }
        
        if (defined('DB_CHARSET')) {
            $credentials['charset'] = DB_CHARSET;
        }
        
        if (defined('DB_COLLATE')) {
            $credentials['collate'] = DB_COLLATE;
        }
        
        // Get table prefix
        global $wpdb;
        if (isset($wpdb->prefix)) {
            $credentials['prefix'] = $wpdb->prefix;
        }
        
        // Check if we need to read wp-config.php (if permission granted)
        if (!BT_Site_Recommendations_Settings::has_permission('read_wp_config')) {
            // Remove password if we can't read config
            $credentials['password'] = '[RESTRICTED]';
        }
        
        return $credentials;
    }
    
    /**
     * Get hosting-specific capabilities and restrictions
     */
    public function get_hosting_capabilities() {
        $hosting = $this->detect();
        $type = $hosting['type'];
        
        $capabilities = array(
            'can_modify_files' => true,
            'can_optimize_db' => true,
            'can_create_tables' => true,
            'can_drop_tables' => true,
            'can_modify_images' => true,
            'has_redis' => false,
            'has_object_cache' => false,
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
        );
        
        switch ($type) {
            case 'pantheon':
                // Pantheon has read-only file system in test/live, restricted DB operations
                $env = defined('PANTHEON_ENVIRONMENT') ? PANTHEON_ENVIRONMENT : 'dev';
                $capabilities['can_modify_files'] = ($env === 'dev');
                $capabilities['can_create_tables'] = false; // Use existing tables only
                $capabilities['can_drop_tables'] = false; // Don't allow dropping tables
                $capabilities['has_redis'] = true;
                $capabilities['has_object_cache'] = true;
                break;
                
            case 'wpengine':
                // WP Engine has some file system restrictions
                $capabilities['can_modify_files'] = true;
                $capabilities['has_object_cache'] = true;
                break;
                
            case 'pressable':
                $capabilities['has_object_cache'] = true;
                break;
                
            case 'godaddy':
                // GoDaddy often has lower limits
                break;
                
            case 'standard':
            default:
                // Standard hosting, assume full capabilities
                break;
        }
        
        return $capabilities;
    }
    
    /**
     * Get hosting display name
     */
    public function get_hosting_display_name($type = null) {
        if ($type === null) {
            $detection = $this->detect();
            $type = $detection['type'];
        }
        
        $names = array(
            'pantheon' => 'Pantheon',
            'wpengine' => 'WP Engine',
            'pressable' => 'Pressable',
            'godaddy' => 'GoDaddy',
            'standard' => 'Standard WordPress Hosting',
        );
        
        return isset($names[$type]) ? $names[$type] : 'Unknown';
    }
    
    /**
     * Get available hosting types for dropdown
     */
    public function get_available_hosting_types() {
        return array(
            'auto' => __('Auto-detect', 'bt-site-recommendations'),
            'pantheon' => __('Pantheon', 'bt-site-recommendations'),
            'wpengine' => __('WP Engine', 'bt-site-recommendations'),
            'pressable' => __('Pressable', 'bt-site-recommendations'),
            'godaddy' => __('GoDaddy', 'bt-site-recommendations'),
            'standard' => __('Standard WordPress Hosting', 'bt-site-recommendations'),
        );
    }
    
    /**
     * Check if wp-config.php is readable
     */
    public function can_read_wp_config() {
        $wp_config_path = ABSPATH . 'wp-config.php';
        
        // Check if file exists and is readable
        if (!file_exists($wp_config_path)) {
            // Try parent directory
            $wp_config_path = dirname(ABSPATH) . '/wp-config.php';
        }
        
        return file_exists($wp_config_path) && is_readable($wp_config_path);
    }
    
    /**
     * Parse wp-config.php for additional information
     */
    public function parse_wp_config() {
        if (!BT_Site_Recommendations_Settings::has_permission('read_wp_config')) {
            return array(
                'success' => false,
                'message' => __('Permission to read wp-config.php is not granted.', 'bt-site-recommendations')
            );
        }
        
        $wp_config_path = ABSPATH . 'wp-config.php';
        
        if (!file_exists($wp_config_path)) {
            $wp_config_path = dirname(ABSPATH) . '/wp-config.php';
        }
        
        if (!file_exists($wp_config_path) || !is_readable($wp_config_path)) {
            return array(
                'success' => false,
                'message' => __('wp-config.php not found or not readable.', 'bt-site-recommendations')
            );
        }
        
        $config_content = file_get_contents($wp_config_path);
        
        $info = array(
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'debug_log' => defined('WP_DEBUG_LOG') && WP_DEBUG_LOG,
            'debug_display' => defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY,
            'memory_limit' => defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : 'default',
            'max_memory_limit' => defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : 'default',
            'cache' => defined('WP_CACHE') && WP_CACHE,
            'auto_update_core' => defined('WP_AUTO_UPDATE_CORE') ? WP_AUTO_UPDATE_CORE : 'default',
            'disallow_file_edit' => defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT,
            'disallow_file_mods' => defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS,
        );
        
        return array(
            'success' => true,
            'data' => $info
        );
    }
    
    /**
     * Get server information
     */
    public function get_server_info() {
        return array(
            'php_version' => PHP_VERSION,
            'mysql_version' => $this->get_mysql_version(),
            'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown',
            'wordpress_version' => get_bloginfo('version'),
            'is_multisite' => is_multisite(),
            'home_url' => home_url(),
            'site_url' => site_url(),
            'wp_content_dir' => WP_CONTENT_DIR,
            'wp_plugin_dir' => WP_PLUGIN_DIR,
            'uploads_dir' => wp_upload_dir(),
        );
    }
    
    /**
     * Get MySQL version
     */
    private function get_mysql_version() {
        global $wpdb;
        $version = $wpdb->get_var('SELECT VERSION()');
        return $version ? $version : 'Unknown';
    }
}

