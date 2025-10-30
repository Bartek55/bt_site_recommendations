<?php
/**
 * Code Analyzer for BT Site Recommendations
 * Analyzes theme and active plugin files for security, performance, and quality issues
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Code_Analyzer {
    
    private $theme_files = array();
    private $plugin_files = array();
    private $file_limit = 100; // Limit number of files to analyze
    private $file_size_limit = 500000; // 500KB per file
    
    /**
     * Run code analysis
     */
    public function analyze() {
        // Check permissions
        if (!BT_Site_Recommendations_Settings::has_permission('read_theme_files') &&
            !BT_Site_Recommendations_Settings::has_permission('read_plugin_files')) {
            return array(
                'success' => false,
                'message' => __('Permission to read code files is not granted.', 'bt-site-recommendations')
            );
        }
        
        // Collect theme files
        if (BT_Site_Recommendations_Settings::has_permission('read_theme_files')) {
            $this->theme_files = $this->get_theme_files();
        }
        
        // Collect plugin files
        if (BT_Site_Recommendations_Settings::has_permission('read_plugin_files')) {
            $this->plugin_files = $this->get_active_plugin_files();
        }
        
        // Prepare data for AI analysis
        $data = $this->prepare_analysis_data();
        
        // Send to AI for analysis
        $ai_manager = new BT_Site_Recommendations_AI_Provider_Manager();
        $result = $ai_manager->analyze($data, 'code');
        
        if (!$result['success']) {
            return $result;
        }
        
        // Enhance results with local analysis
        $enhanced_data = $this->enhance_with_local_analysis($result['data']);
        
        return array(
            'success' => true,
            'data' => $enhanced_data,
            'message' => __('Code analysis completed successfully.', 'bt-site-recommendations')
        );
    }
    
    /**
     * Get active theme files
     */
    private function get_theme_files() {
        $theme = wp_get_theme();
        $theme_dir = $theme->get_stylesheet_directory();
        
        $files = $this->scan_directory($theme_dir, array('php', 'js'));
        
        return array(
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'path' => $theme_dir,
            'files' => $files,
        );
    }
    
    /**
     * Get active plugin files
     */
    private function get_active_plugin_files() {
        $active_plugins = get_option('active_plugins', array());
        $plugins = array();
        
        foreach ($active_plugins as $plugin_path) {
            $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($plugin_path);
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
            
            // Skip if not a directory (single file plugin)
            if (!is_dir($plugin_dir)) {
                $plugin_dir = WP_PLUGIN_DIR;
                $files = array($this->get_file_data(WP_PLUGIN_DIR . '/' . $plugin_path));
            } else {
                $files = $this->scan_directory($plugin_dir, array('php', 'js'));
            }
            
            $plugins[] = array(
                'name' => $plugin_data['Name'],
                'version' => $plugin_data['Version'],
                'path' => $plugin_dir,
                'files' => $files,
            );
        }
        
        return $plugins;
    }
    
    /**
     * Scan directory for files
     */
    private function scan_directory($dir, $extensions = array('php')) {
        $files = array();
        $count = 0;
        
        if (!is_dir($dir) || !is_readable($dir)) {
            return $files;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($count >= $this->file_limit) {
                break;
            }
            
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                
                if (in_array($extension, $extensions)) {
                    $file_path = $file->getPathname();
                    $file_size = $file->getSize();
                    
                    // Skip very large files
                    if ($file_size > $this->file_size_limit) {
                        continue;
                    }
                    
                    // Skip vendor and node_modules directories
                    if (strpos($file_path, '/vendor/') !== false || 
                        strpos($file_path, '/node_modules/') !== false) {
                        continue;
                    }
                    
                    $files[] = $this->get_file_data($file_path);
                    $count++;
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Get file data
     */
    private function get_file_data($file_path) {
        $content = '';
        
        if (is_readable($file_path)) {
            $content = file_get_contents($file_path);
            
            // Sanitize content - remove sensitive data patterns
            $content = $this->sanitize_code_content($content);
        }
        
        return array(
            'path' => str_replace(ABSPATH, '', $file_path),
            'full_path' => $file_path,
            'name' => basename($file_path),
            'size' => filesize($file_path),
            'modified' => filemtime($file_path),
            'content' => $content,
        );
    }
    
    /**
     * Sanitize code content before sending to AI
     */
    private function sanitize_code_content($content) {
        // Remove potential API keys, passwords, tokens
        $patterns = array(
            // API keys
            '/[\'"]api[_-]?key[\'"]\s*[=:>]\s*[\'"][^\'"]{20,}[\'"]/i',
            '/[\'"]apikey[\'"]\s*[=:>]\s*[\'"][^\'"]{20,}[\'"]/i',
            // Passwords
            '/[\'"]password[\'"]\s*[=:>]\s*[\'"][^\'"]+[\'"]/i',
            '/[\'"]pass[\'"]\s*[=:>]\s*[\'"][^\'"]+[\'"]/i',
            // Tokens
            '/[\'"]token[\'"]\s*[=:>]\s*[\'"][^\'"]{20,}[\'"]/i',
            '/[\'"]secret[\'"]\s*[=:>]\s*[\'"][^\'"]{20,}[\'"]/i',
            // Database credentials (already handled by wp-config but just in case)
            '/define\s*\(\s*[\'"]DB_PASSWORD[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*\)/i',
        );
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '[REDACTED]', $content);
        }
        
        return $content;
    }
    
    /**
     * Prepare data for AI analysis
     */
    private function prepare_analysis_data() {
        return array(
            'theme' => $this->theme_files,
            'plugins' => $this->plugin_files,
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
        );
    }
    
    /**
     * Enhance AI results with local analysis
     */
    private function enhance_with_local_analysis($ai_data) {
        // Add local checks that don't require AI
        $local_checks = $this->run_local_checks();
        
        // Merge with AI results
        if (isset($ai_data['security_issues'])) {
            $ai_data['security_issues'] = array_merge(
                $ai_data['security_issues'],
                $local_checks['security_issues']
            );
        } else {
            $ai_data['security_issues'] = $local_checks['security_issues'];
        }
        
        if (isset($ai_data['deprecated_functions'])) {
            $ai_data['deprecated_functions'] = array_merge(
                $ai_data['deprecated_functions'],
                $local_checks['deprecated_functions']
            );
        } else {
            $ai_data['deprecated_functions'] = $local_checks['deprecated_functions'];
        }
        
        return $ai_data;
    }
    
    /**
     * Run local checks (without AI)
     */
    private function run_local_checks() {
        $security_issues = array();
        $deprecated_functions = array();
        
        // Common deprecated WordPress functions
        $deprecated_wp_functions = array(
            'get_settings' => 'get_option',
            'get_option_update' => 'update_option',
            'get_catname' => 'get_cat_name',
            'get_the_category_list' => 'get_the_category',
            'wp_get_post_categories' => 'wp_get_post_terms',
            'get_link' => 'get_bookmark',
            'get_usermeta' => 'get_user_meta',
            'update_usermeta' => 'update_user_meta',
        );
        
        // Check theme files
        if (!empty($this->theme_files['files'])) {
            foreach ($this->theme_files['files'] as $file) {
                $this->check_file_for_issues($file, $deprecated_wp_functions, $security_issues, $deprecated_functions);
            }
        }
        
        // Check plugin files
        foreach ($this->plugin_files as $plugin) {
            if (!empty($plugin['files'])) {
                foreach ($plugin['files'] as $file) {
                    $this->check_file_for_issues($file, $deprecated_wp_functions, $security_issues, $deprecated_functions);
                }
            }
        }
        
        return array(
            'security_issues' => $security_issues,
            'deprecated_functions' => $deprecated_functions,
        );
    }
    
    /**
     * Check individual file for issues
     */
    private function check_file_for_issues($file, $deprecated_functions, &$security_issues, &$deprecated_list) {
        $content = $file['content'];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line_num => $line) {
            $line_number = $line_num + 1;
            
            // Check for deprecated functions
            foreach ($deprecated_functions as $old_func => $new_func) {
                if (strpos($line, $old_func . '(') !== false) {
                    $deprecated_list[] = array(
                        'file' => $file['path'],
                        'line' => $line_number,
                        'function' => $old_func,
                        'replacement' => $new_func,
                    );
                }
            }
            
            // Check for common security issues
            
            // SQL injection risks
            if (preg_match('/\$wpdb->query\s*\(\s*["\'].*\$/', $line) ||
                preg_match('/\$wpdb->get_results\s*\(\s*["\'].*\$/', $line)) {
                if (strpos($line, '$wpdb->prepare') === false) {
                    $security_issues[] = array(
                        'file' => $file['path'],
                        'line' => $line_number,
                        'severity' => 'high',
                        'issue' => 'Potential SQL injection - unsanitized variable in query',
                        'fix' => 'Use $wpdb->prepare() to sanitize queries',
                    );
                }
            }
            
            // XSS risks
            if (preg_match('/echo\s+\$_(?:GET|POST|REQUEST|COOKIE)/', $line) ||
                preg_match('/print\s+\$_(?:GET|POST|REQUEST|COOKIE)/', $line)) {
                if (strpos($line, 'esc_html') === false &&
                    strpos($line, 'esc_attr') === false &&
                    strpos($line, 'esc_url') === false &&
                    strpos($line, 'sanitize_') === false) {
                    $security_issues[] = array(
                        'file' => $file['path'],
                        'line' => $line_number,
                        'severity' => 'critical',
                        'issue' => 'Potential XSS - unsanitized output of user input',
                        'fix' => 'Use esc_html(), esc_attr(), or esc_url() before output',
                    );
                }
            }
            
            // File inclusion risks
            if (preg_match('/(include|require)(_once)?\s*\(\s*\$_(?:GET|POST|REQUEST)/', $line)) {
                $security_issues[] = array(
                    'file' => $file['path'],
                    'line' => $line_number,
                    'severity' => 'critical',
                    'issue' => 'Potential file inclusion vulnerability',
                    'fix' => 'Never use user input directly in include/require statements',
                );
            }
            
            // eval() usage
            if (preg_match('/\beval\s*\(/', $line)) {
                $security_issues[] = array(
                    'file' => $file['path'],
                    'line' => $line_number,
                    'severity' => 'high',
                    'issue' => 'Use of eval() detected - potential security risk',
                    'fix' => 'Avoid using eval() - refactor code to eliminate its need',
                );
            }
        }
    }
    
    /**
     * Get summary statistics
     */
    public function get_statistics() {
        $stats = array(
            'theme_files_count' => 0,
            'plugin_files_count' => 0,
            'total_lines' => 0,
            'total_size' => 0,
        );
        
        if (!empty($this->theme_files['files'])) {
            $stats['theme_files_count'] = count($this->theme_files['files']);
            foreach ($this->theme_files['files'] as $file) {
                $stats['total_size'] += $file['size'];
                $stats['total_lines'] += substr_count($file['content'], "\n");
            }
        }
        
        foreach ($this->plugin_files as $plugin) {
            if (!empty($plugin['files'])) {
                $stats['plugin_files_count'] += count($plugin['files']);
                foreach ($plugin['files'] as $file) {
                    $stats['total_size'] += $file['size'];
                    $stats['total_lines'] += substr_count($file['content'], "\n");
                }
            }
        }
        
        return $stats;
    }
}

