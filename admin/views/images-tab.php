<?php
/**
 * Images Analysis Tab View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bt-sr-images-tab">
    
    <!-- Analysis Controls -->
    <div class="bt-sr-card">
        <h2><?php _e('Image Analysis & Optimization', 'bt-site-recommendations'); ?></h2>
        <p><?php _e('Scan your media library for optimization opportunities, format conversion, and accessibility improvements.', 'bt-site-recommendations'); ?></p>
        
        <button type="button" id="bt-sr-scan-images" class="button button-primary button-large">
            <span class="dashicons dashicons-format-image"></span>
            <?php _e('Scan Images', 'bt-site-recommendations'); ?>
        </button>
        
        <button type="button" id="bt-sr-force-image-scan" class="button button-secondary">
            <span class="dashicons dashicons-update"></span>
            <?php _e('Force Re-scan (Clear Cache)', 'bt-site-recommendations'); ?>
        </button>
    </div>
    
    <!-- Progress Indicator -->
    <div id="bt-sr-image-progress" class="bt-sr-card bt-sr-progress-card" style="display: none;">
        <h3><?php _e('Scanning...', 'bt-site-recommendations'); ?></h3>
        <div class="bt-sr-progress-bar">
            <div class="bt-sr-progress-fill"></div>
        </div>
        <p class="bt-sr-progress-text"><?php _e('This may take a few moments...', 'bt-site-recommendations'); ?></p>
    </div>
    
    <!-- Results Container -->
    <div id="bt-sr-image-results" style="display: none;">
        
        <!-- Statistics -->
        <div class="bt-sr-card bt-sr-stats-card">
            <h2><?php _e('Statistics', 'bt-site-recommendations'); ?></h2>
            <div class="bt-sr-stats-grid">
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('Total Images:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-total-images">0</span>
                </div>
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('Total Size:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-total-size">0 B</span>
                </div>
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('Potential Savings:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-potential-savings">0 B</span>
                </div>
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('WebP Candidates:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-webp-candidates">0</span>
                </div>
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('Missing Alt Text:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-missing-alt">0</span>
                </div>
                <div class="bt-sr-stat">
                    <span class="bt-sr-stat-label"><?php _e('Unused Images:', 'bt-site-recommendations'); ?></span>
                    <span class="bt-sr-stat-value" id="bt-sr-unused-images">0</span>
                </div>
            </div>
        </div>
        
        <!-- Size Optimization -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-chart-area"></span>
                <?php _e('Size Optimization Opportunities', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-size-optimization">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Format Conversion -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-images-alt2"></span>
                <?php _e('Format Conversion (WebP)', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-format-conversion">
                <!-- Populated by JavaScript -->
            </div>
            <div class="bt-sr-batch-actions">
                <button type="button" class="button button-primary bt-sr-batch-convert-webp">
                    <span class="dashicons dashicons-update-alt"></span>
                    <?php _e('Convert Selected to WebP', 'bt-site-recommendations'); ?>
                </button>
                <button type="button" class="button button-secondary bt-sr-select-all-webp">
                    <?php _e('Select All', 'bt-site-recommendations'); ?>
                </button>
            </div>
        </div>
        
        <!-- Missing Alt Text -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-universal-access-alt"></span>
                <?php _e('Missing Alt Text (SEO & Accessibility)', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('Alt text improves SEO, helps visually impaired users, and provides context when images fail to load.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-missing-alt-text">
                <!-- Populated by JavaScript -->
            </div>
            <div class="bt-sr-batch-actions">
                <button type="button" class="button button-primary bt-sr-batch-add-alt">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Add Alt Text to Selected', 'bt-site-recommendations'); ?>
                </button>
                <button type="button" class="button button-secondary bt-sr-select-all-alt">
                    <?php _e('Select All', 'bt-site-recommendations'); ?>
                </button>
            </div>
        </div>
        
        <!-- Lazy Loading -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-image-filter"></span>
                <?php _e('Lazy Loading', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('Lazy loading defers offscreen images, improving initial page load speed.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-lazy-loading">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Unused Images -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-trash"></span>
                <?php _e('Unused Images', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-unused-images-list">
                <!-- Populated by JavaScript -->
            </div>
            <div class="bt-sr-batch-actions">
                <p class="description">
                    <?php _e('These images are not currently used in any posts or pages. Consider removing them to free up space.', 'bt-site-recommendations'); ?>
                </p>
            </div>
        </div>
        
        <!-- Page Speed & SEO Impact -->
        <div class="bt-sr-card bt-sr-highlight-card">
            <h2>
                <span class="dashicons dashicons-chart-line"></span>
                <?php _e('Optimization Impact', 'bt-site-recommendations'); ?>
            </h2>
            <div id="bt-sr-image-improvements">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Recommended Plugins -->
        <div class="bt-sr-card">
            <h2>
                <span class="dashicons dashicons-admin-plugins"></span>
                <?php _e('Recommended Image Optimization Plugins', 'bt-site-recommendations'); ?>
            </h2>
            <p><?php _e('These plugins can help automate image optimization and improve page speed.', 'bt-site-recommendations'); ?></p>
            <div id="bt-sr-image-recommended-plugins">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Summary -->
        <div class="bt-sr-card bt-sr-summary-card">
            <h2><?php _e('Summary', 'bt-site-recommendations'); ?></h2>
            <div id="bt-sr-image-summary">
                <!-- Populated by JavaScript -->
            </div>
        </div>
        
        <!-- Batch Progress -->
        <div id="bt-sr-batch-progress" class="bt-sr-card" style="display: none;">
            <h3><?php _e('Processing Images...', 'bt-site-recommendations'); ?></h3>
            <div class="bt-sr-progress-bar">
                <div class="bt-sr-progress-fill"></div>
            </div>
            <p class="bt-sr-progress-text"></p>
            <div id="bt-sr-batch-results"></div>
        </div>
    </div>
</div>

