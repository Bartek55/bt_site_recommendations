<?php
/**
 * Database Analyzer for BT Site Recommendations
 * Analyzes database schema, performance, security, and integrity
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Database_Analyzer {
    
    private $wpdb;
    private $hosting_detector;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->hosting_detector = new BT_Site_Recommendations_Hosting_Detector();
    }
    
    /**
     * Run database analysis
     */
    public function analyze() {
        // Check permissions
        if (!BT_Site_Recommendations_Settings::has_permission('access_db_structure') &&
            !BT_Site_Recommendations_Settings::has_permission('query_db_content')) {
            return array(
                'success' => false,
                'message' => __('Permission to access database is not granted.', 'bt-site-recommendations')
            );
        }
        
        // Collect database information
        $data = $this->collect_database_info();
        
        // Send to AI for analysis
        $ai_manager = new BT_Site_Recommendations_AI_Provider_Manager();
        $result = $ai_manager->analyze($data, 'database');
        
        if (!$result['success']) {
            return $result;
        }
        
        // Enhance with local analysis
        $enhanced_data = $this->enhance_with_local_analysis($result['data'], $data);
        
        return array(
            'success' => true,
            'data' => $enhanced_data,
            'message' => __('Database analysis completed successfully.', 'bt-site-recommendations')
        );
    }
    
    /**
     * Collect database information
     */
    private function collect_database_info() {
        $data = array(
            'tables' => array(),
            'total_size' => 0,
            'wp_version' => get_bloginfo('version'),
            'slow_queries' => array(),
        );
        
        // Get table information
        if (BT_Site_Recommendations_Settings::has_permission('access_db_structure')) {
            $data['tables'] = $this->get_table_info();
            $data['total_size'] = $this->format_size($this->calculate_total_size($data['tables']));
        }
        
        // Get slow query information (if available)
        if (BT_Site_Recommendations_Settings::has_permission('query_db_content')) {
            $data['slow_queries'] = $this->detect_slow_queries();
        }
        
        return $data;
    }
    
    /**
     * Get table information
     */
    private function get_table_info() {
        $tables = array();
        
        // Get all tables
        $table_query = "SHOW TABLE STATUS";
        $results = $this->wpdb->get_results($table_query);
        
        if (!$results) {
            return $tables;
        }
        
        foreach ($results as $table) {
            $table_name = $table->Name;
            
            // Get indexes
            $indexes = $this->get_table_indexes($table_name);
            
            // Get column information
            $columns = $this->get_table_columns($table_name);
            
            $tables[] = array(
                'name' => $table_name,
                'engine' => $table->Engine,
                'rows' => $table->Rows,
                'avg_row_length' => $table->Avg_row_length,
                'data_length' => $table->Data_length,
                'index_length' => $table->Index_length,
                'size' => $this->format_size($table->Data_length + $table->Index_length),
                'collation' => $table->Collation,
                'indexes' => $indexes,
                'columns' => $columns,
                'auto_increment' => $table->Auto_increment,
                'create_time' => $table->Create_time,
                'update_time' => $table->Update_time,
            );
        }
        
        return $tables;
    }
    
    /**
     * Get table indexes
     */
    private function get_table_indexes($table_name) {
        $indexes = array();
        
        $query = $this->wpdb->prepare("SHOW INDEX FROM `%s`", $table_name);
        // Note: prepare doesn't work with table names, use direct query with escaping
        $query = "SHOW INDEX FROM `" . esc_sql($table_name) . "`";
        $results = $this->wpdb->get_results($query);
        
        if ($results) {
            foreach ($results as $index) {
                $indexes[] = $index->Key_name;
            }
            $indexes = array_unique($indexes);
        }
        
        return $indexes;
    }
    
    /**
     * Get table columns
     */
    private function get_table_columns($table_name) {
        $columns = array();
        
        $query = "SHOW COLUMNS FROM `" . esc_sql($table_name) . "`";
        $results = $this->wpdb->get_results($query);
        
        if ($results) {
            foreach ($results as $column) {
                $columns[] = array(
                    'name' => $column->Field,
                    'type' => $column->Type,
                    'null' => $column->Null,
                    'key' => $column->Key,
                    'default' => $column->Default,
                    'extra' => $column->Extra,
                );
            }
        }
        
        return $columns;
    }
    
    /**
     * Detect slow queries (simplified - would need query logging enabled)
     */
    private function detect_slow_queries() {
        $slow_queries = array();
        
        // Check if slow query log is enabled
        $slow_query_log = $this->wpdb->get_var("SHOW VARIABLES LIKE 'slow_query_log'");
        
        if (!$slow_query_log) {
            return $slow_queries;
        }
        
        // This is a placeholder - actual implementation would require
        // access to slow query log file or performance_schema
        
        return $slow_queries;
    }
    
    /**
     * Calculate total database size
     */
    private function calculate_total_size($tables) {
        $total = 0;
        
        foreach ($tables as $table) {
            $total += $table['data_length'] + $table['index_length'];
        }
        
        return $total;
    }
    
    /**
     * Format size in human-readable format
     */
    private function format_size($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Enhance with local analysis
     */
    private function enhance_with_local_analysis($ai_data, $raw_data) {
        $local_checks = $this->run_local_checks($raw_data);
        
        // Merge local checks with AI analysis
        foreach ($local_checks as $category => $items) {
            if (isset($ai_data[$category])) {
                $ai_data[$category] = array_merge($ai_data[$category], $items);
            } else {
                $ai_data[$category] = $items;
            }
        }
        
        return $ai_data;
    }
    
    /**
     * Run local database checks
     */
    private function run_local_checks($data) {
        $checks = array(
            'schema_optimization' => array(),
            'data_cleanup' => array(),
            'security_issues' => array(),
            'data_integrity' => array(),
        );
        
        // Check for tables without indexes
        foreach ($data['tables'] as $table) {
            if (empty($table['indexes']) && $table['rows'] > 100) {
                $checks['schema_optimization'][] = array(
                    'table' => $table['name'],
                    'issue' => 'No indexes found on table with ' . $table['rows'] . ' rows',
                    'recommendation' => 'Add appropriate indexes to improve query performance',
                    'severity' => 'warning',
                );
            }
            
            // Check for MyISAM engine (InnoDB is preferred)
            if ($table['engine'] === 'MyISAM') {
                $checks['schema_optimization'][] = array(
                    'table' => $table['name'],
                    'issue' => 'Table uses MyISAM engine',
                    'recommendation' => 'Consider converting to InnoDB for better performance and ACID compliance',
                    'severity' => 'info',
                );
            }
            
            // Check for tables with high overhead
            if (isset($table['data_length']) && $table['rows'] > 0) {
                $avg_row = $table['avg_row_length'];
                if ($avg_row > 10000) { // Average row > 10KB
                    $checks['schema_optimization'][] = array(
                        'table' => $table['name'],
                        'issue' => 'Table has large average row size (' . $this->format_size($avg_row) . ')',
                        'recommendation' => 'Consider normalizing data or moving large fields to separate tables',
                        'severity' => 'info',
                    );
                }
            }
        }
        
        // Check for common cleanup opportunities
        $checks['data_cleanup'] = array_merge(
            $checks['data_cleanup'],
            $this->check_transients(),
            $this->check_post_revisions(),
            $this->check_trashed_items(),
            $this->check_orphaned_metadata()
        );
        
        // Security checks
        $checks['security_issues'] = array_merge(
            $checks['security_issues'],
            $this->check_user_privileges()
        );
        
        return $checks;
    }
    
    /**
     * Check for expired transients
     */
    private function check_transients() {
        $issues = array();
        
        $expired_count = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->options} 
            WHERE option_name LIKE '_transient_timeout_%' 
            AND option_value < UNIX_TIMESTAMP()"
        );
        
        if ($expired_count > 100) {
            $issues[] = array(
                'type' => 'transients',
                'items' => array('Expired transients: ' . $expired_count),
                'recommendation' => 'Delete expired transients to clean up database',
            );
        }
        
        return $issues;
    }
    
    /**
     * Check for excessive post revisions
     */
    private function check_post_revisions() {
        $issues = array();
        
        $revision_count = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_type = 'revision'"
        );
        
        if ($revision_count > 1000) {
            $issues[] = array(
                'type' => 'post_revisions',
                'items' => array('Post revisions: ' . $revision_count),
                'recommendation' => 'Consider limiting post revisions or cleaning up old ones',
            );
        }
        
        return $issues;
    }
    
    /**
     * Check for trashed items
     */
    private function check_trashed_items() {
        $issues = array();
        
        $trashed_posts = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_status = 'trash'"
        );
        
        $trashed_comments = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->comments} WHERE comment_approved = 'trash'"
        );
        
        if ($trashed_posts > 50 || $trashed_comments > 100) {
            $items = array();
            if ($trashed_posts > 50) {
                $items[] = 'Trashed posts: ' . $trashed_posts;
            }
            if ($trashed_comments > 100) {
                $items[] = 'Trashed comments: ' . $trashed_comments;
            }
            
            $issues[] = array(
                'type' => 'trashed_items',
                'items' => $items,
                'recommendation' => 'Permanently delete trashed items to free up space',
            );
        }
        
        return $issues;
    }
    
    /**
     * Check for orphaned metadata
     */
    private function check_orphaned_metadata() {
        $issues = array();
        
        // Check for orphaned postmeta
        $orphaned_postmeta = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->postmeta} pm 
            LEFT JOIN {$this->wpdb->posts} p ON pm.post_id = p.ID 
            WHERE p.ID IS NULL"
        );
        
        // Check for orphaned user meta (from deleted users)
        $orphaned_usermeta = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->usermeta} um 
            LEFT JOIN {$this->wpdb->users} u ON um.user_id = u.ID 
            WHERE u.ID IS NULL"
        );
        
        if ($orphaned_postmeta > 0 || $orphaned_usermeta > 0) {
            $items = array();
            if ($orphaned_postmeta > 0) {
                $items[] = 'Orphaned postmeta: ' . $orphaned_postmeta;
            }
            if ($orphaned_usermeta > 0) {
                $items[] = 'Orphaned usermeta: ' . $orphaned_usermeta;
            }
            
            $issues[] = array(
                'type' => 'orphaned_records',
                'items' => $items,
                'recommendation' => 'Clean up orphaned metadata records',
            );
        }
        
        return $issues;
    }
    
    /**
     * Check user privileges
     */
    private function check_user_privileges() {
        $issues = array();
        
        // Check for admin users
        $admin_count = $this->wpdb->get_var(
            "SELECT COUNT(DISTINCT um.user_id) FROM {$this->wpdb->usermeta} um 
            WHERE um.meta_key = '{$this->wpdb->prefix}capabilities' 
            AND um.meta_value LIKE '%administrator%'"
        );
        
        if ($admin_count > 5) {
            $issues[] = array(
                'issue' => 'High number of administrator accounts (' . $admin_count . ')',
                'severity' => 'warning',
                'recommendation' => 'Review administrator accounts and revoke unnecessary privileges',
            );
        }
        
        return $issues;
    }
    
    /**
     * Get database statistics
     */
    public function get_statistics() {
        $data = $this->collect_database_info();
        
        return array(
            'total_tables' => count($data['tables']),
            'total_size' => $data['total_size'],
            'wp_tables' => $this->count_wp_tables($data['tables']),
            'custom_tables' => $this->count_custom_tables($data['tables']),
        );
    }
    
    /**
     * Count WordPress core tables
     */
    private function count_wp_tables($tables) {
        $count = 0;
        $wp_prefix = $this->wpdb->prefix;
        
        foreach ($tables as $table) {
            if (strpos($table['name'], $wp_prefix) === 0) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Count custom tables
     */
    private function count_custom_tables($tables) {
        return count($tables) - $this->count_wp_tables($tables);
    }
    
    /**
     * Apply safe automated fixes
     */
    public function apply_safe_fixes($fixes) {
        $results = array(
            'success' => true,
            'applied' => array(),
            'errors' => array(),
        );
        
        // Check hosting capabilities
        $capabilities = $this->hosting_detector->get_hosting_capabilities();
        
        foreach ($fixes as $fix) {
            $fix_type = isset($fix['type']) ? $fix['type'] : '';
            
            switch ($fix_type) {
                case 'delete_transients':
                    if ($this->delete_expired_transients()) {
                        $results['applied'][] = 'Deleted expired transients';
                    } else {
                        $results['errors'][] = 'Failed to delete transients';
                    }
                    break;
                    
                case 'delete_trashed_posts':
                    if ($this->delete_trashed_posts()) {
                        $results['applied'][] = 'Deleted trashed posts';
                    } else {
                        $results['errors'][] = 'Failed to delete trashed posts';
                    }
                    break;
                    
                case 'delete_orphaned_postmeta':
                    if ($this->delete_orphaned_postmeta()) {
                        $results['applied'][] = 'Deleted orphaned postmeta';
                    } else {
                        $results['errors'][] = 'Failed to delete orphaned postmeta';
                    }
                    break;
                    
                case 'optimize_tables':
                    if ($capabilities['can_optimize_db']) {
                        if ($this->optimize_tables()) {
                            $results['applied'][] = 'Optimized database tables';
                        } else {
                            $results['errors'][] = 'Failed to optimize tables';
                        }
                    } else {
                        $results['errors'][] = 'Database optimization not allowed on this hosting';
                    }
                    break;
            }
        }
        
        if (!empty($results['errors'])) {
            $results['success'] = false;
        }
        
        return $results;
    }
    
    /**
     * Delete expired transients
     */
    private function delete_expired_transients() {
        $deleted = $this->wpdb->query(
            "DELETE FROM {$this->wpdb->options} 
            WHERE option_name LIKE '_transient_timeout_%' 
            AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Also delete the transient values
        $this->wpdb->query(
            "DELETE FROM {$this->wpdb->options} 
            WHERE option_name LIKE '_transient_%' 
            AND option_name NOT LIKE '_transient_timeout_%'
            AND option_name NOT IN (
                SELECT REPLACE(option_name, '_timeout', '') 
                FROM {$this->wpdb->options} 
                WHERE option_name LIKE '_transient_timeout_%'
            )"
        );
        
        return $deleted !== false;
    }
    
    /**
     * Delete trashed posts
     */
    private function delete_trashed_posts() {
        $deleted = $this->wpdb->query(
            "DELETE FROM {$this->wpdb->posts} WHERE post_status = 'trash'"
        );
        
        return $deleted !== false;
    }
    
    /**
     * Delete orphaned postmeta
     */
    private function delete_orphaned_postmeta() {
        $deleted = $this->wpdb->query(
            "DELETE pm FROM {$this->wpdb->postmeta} pm 
            LEFT JOIN {$this->wpdb->posts} p ON pm.post_id = p.ID 
            WHERE p.ID IS NULL"
        );
        
        return $deleted !== false;
    }
    
    /**
     * Optimize database tables
     */
    private function optimize_tables() {
        $tables = $this->wpdb->get_col("SHOW TABLES");
        
        foreach ($tables as $table) {
            $this->wpdb->query("OPTIMIZE TABLE `" . esc_sql($table) . "`");
        }
        
        return true;
    }
}

