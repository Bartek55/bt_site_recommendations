<?php
/**
 * Database Analysis Tab View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bt-sr-database-tab">
    
    <!-- Analysis Controls -->
    <div class="bt-sr-card">
        <h2><?php _e('Database Analysis', 'bt-site-recommendations'); ?></h2>
        <p><?php _e('Analyze your database for optimization opportunities, security issues, and data integrity concerns.', 'bt-site-recommendations'); ?></p>
        
        <button type="button" id="bt-sr-run-db-analysis" class="button button-primary button-large">
            <span class="dashicons dashicons-database"></span>
            <?php _e('Run Database Analysis', 'bt-site-recommendations'); ?>
        </button>
        
        <button type="button" id="bt-sr-force-db-analysis" class="button button-secondary">
            <span class="dashicons dashicons-update"></span>
            <?php _e('Force Re-analyze (Clear Cache)', 'bt-site-recommendations'); ?>
        </button>
    </div>
    
    <!-- Progress Indicator -->
    <div id="bt-sr-db-progress" class="bt-sr-card bt-sr-progress-card" style="display: none;">
        <h3><?php _e('Analyzing...', 'bt-site-recommendations'); ?></h3>
        <div class="bt-sr-progress-bar">
            <div class="bt-sr-progress-fill"></div>
        </div>
        <p class="bt-sr-progress-text"><?php _e('This may take a few moments...', 'bt-site-recommendations'); ?></p>
    </div>
    
    <!-- Results Container -->
    <div id="bt-sr-db-results" style="display: none;">
        
        <!-- Performance & Optimization -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-performance"></span>
                <?php _e('Performance & Optimization', 'bt-site-recommendations'); ?>
            </h2>
            
            <!-- Schema Optimization -->
            <div class="bt-sr-subsection">
                <h3><?php _e('Schema Optimization', 'bt-site-recommendations'); ?></h3>
                <div id="bt-sr-schema-optimization">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
            
            <!-- Query Performance -->
            <div class="bt-sr-subsection">
                <h3><?php _e('Query Performance', 'bt-site-recommendations'); ?></h3>
                <div id="bt-sr-query-performance">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
            
            <!-- Data Cleanup -->
            <div class="bt-sr-subsection">
                <h3><?php _e('Data Cleanup', 'bt-site-recommendations'); ?></h3>
                <div id="bt-sr-data-cleanup">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Security & Integrity -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-shield"></span>
                <?php _e('Security & Data Integrity', 'bt-site-recommendations'); ?>
            </h2>
            
            <!-- Security Issues -->
            <div class="bt-sr-subsection">
                <h3><?php _e('Security Issues', 'bt-site-recommendations'); ?></h3>
                <div id="bt-sr-security-checks">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
            
            <!-- Data Integrity -->
            <div class="bt-sr-subsection">
                <h3><?php _e('Data Integrity', 'bt-site-recommendations'); ?></h3>
                <div id="bt-sr-data-integrity">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Summary -->
        <div class="bt-sr-card bt-sr-summary-card">
            <h2><?php _e('Summary', 'bt-site-recommendations'); ?></h2>
            <div id="bt-sr-db-summary">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Actions -->
        <div class="bt-sr-card">
            <button type="button" id="bt-sr-apply-safe-fixes" class="button button-primary">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php _e('Apply Safe Fixes', 'bt-site-recommendations'); ?>
            </button>
            
            <button type="button" id="bt-sr-download-db-report" class="button button-secondary">
                <span class="dashicons dashicons-download"></span>
                <?php _e('Export Report', 'bt-site-recommendations'); ?>
            </button>
            
            <p class="description">
                <?php _e('Safe fixes include: deleting expired transients, removing orphaned metadata, and optimizing tables.', 'bt-site-recommendations'); ?>
            </p>
        </div>
    </div>
</div>

