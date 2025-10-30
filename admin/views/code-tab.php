<?php
/**
 * Code Analysis Tab View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bt-sr-code-tab">
    
    <!-- Analysis Controls -->
    <div class="bt-sr-card">
        <h2><?php _e('Code Analysis', 'bt-site-recommendations'); ?></h2>
        <p><?php _e('Analyze your active theme and plugin files for security vulnerabilities, deprecated functions, and performance issues.', 'bt-site-recommendations'); ?></p>
        
        <button type="button" id="bt-sr-run-code-analysis" class="button button-primary button-large">
            <span class="dashicons dashicons-search"></span>
            <?php _e('Run Code Analysis', 'bt-site-recommendations'); ?>
        </button>
        
        <button type="button" id="bt-sr-force-code-analysis" class="button button-secondary">
            <span class="dashicons dashicons-update"></span>
            <?php _e('Force Re-analyze (Clear Cache)', 'bt-site-recommendations'); ?>
        </button>
    </div>
    
    <!-- Progress Indicator -->
    <div id="bt-sr-code-progress" class="bt-sr-card bt-sr-progress-card" style="display: none;">
        <h3><?php _e('Analyzing...', 'bt-site-recommendations'); ?></h3>
        <div class="bt-sr-progress-bar">
            <div class="bt-sr-progress-fill"></div>
        </div>
        <p class="bt-sr-progress-text"><?php _e('This may take a few moments...', 'bt-site-recommendations'); ?></p>
    </div>
    
    <!-- Results Container -->
    <div id="bt-sr-code-results" style="display: none;">
        
        <!-- Page Speed Score -->
        <div class="bt-sr-card bt-sr-highlight-card">
            <div id="bt-sr-page-speed-score">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Security Issues -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-shield-alt"></span>
                <?php _e('Security Issues', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-security-issues">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Deprecated Functions -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-warning"></span>
                <?php _e('Deprecated Functions', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-deprecated-functions">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Performance Issues -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-performance"></span>
                <?php _e('Page Speed & Performance', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('Issues that affect page loading speed and Core Web Vitals.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-performance-issues">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- SEO Issues -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-search"></span>
                <?php _e('SEO Issues', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('Code-related issues that affect search engine optimization.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-seo-issues">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Code Quality -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-editor-code"></span>
                <?php _e('Code Quality Suggestions', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-code-quality">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Recommended Plugins -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-admin-plugins"></span>
                <?php _e('Recommended Plugins', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('WordPress plugins that can help improve your site\'s performance and SEO.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-recommended-plugins">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Summary -->
        <div class="bt-sr-card bt-sr-summary-card">
            <h2><?php _e('Summary', 'bt-site-recommendations'); ?></h2>
            <div id="bt-sr-code-summary">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Actions -->
        <div class="bt-sr-card">
            <button type="button" id="bt-sr-download-code-report" class="button button-secondary">
                <span class="dashicons dashicons-download"></span>
                <?php _e('Download Report', 'bt-site-recommendations'); ?>
            </button>
            
            <button type="button" id="bt-sr-copy-code-report" class="button button-secondary">
                <span class="dashicons dashicons-clipboard"></span>
                <?php _e('Copy to Clipboard', 'bt-site-recommendations'); ?>
            </button>
        </div>
    </div>
</div>

