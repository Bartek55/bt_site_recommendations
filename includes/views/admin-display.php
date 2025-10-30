<?php
/**
 * Admin display view.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap bt-site-recommendations">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="bt-container">
        <div class="bt-header">
            <p class="bt-intro">
                <?php _e('Get AI-powered recommendations to improve your site\'s performance and SEO. Click the button below to analyze your site.', 'bt-site-recommendations'); ?>
            </p>
            <button type="button" id="bt-analyze-btn" class="button button-primary button-hero">
                <span class="dashicons dashicons-chart-area"></span>
                <?php _e('Analyze My Site', 'bt-site-recommendations'); ?>
            </button>
        </div>

        <div id="bt-loading" style="display: none;">
            <div class="bt-spinner">
                <span class="spinner is-active"></span>
                <p><?php _e('Analyzing your site... This may take a moment.', 'bt-site-recommendations'); ?></p>
            </div>
        </div>

        <div id="bt-results" style="display: none;">
            <div class="bt-results-header">
                <h2><?php _e('Analysis Results', 'bt-site-recommendations'); ?></h2>
                <p class="bt-results-date"><?php _e('Analysis completed:', 'bt-site-recommendations'); ?> <span id="bt-analysis-date"></span></p>
            </div>

            <!-- Metrics Summary -->
            <div class="bt-metrics-grid">
                <div class="bt-metric-card">
                    <div class="bt-metric-icon">
                        <span class="dashicons dashicons-dashboard"></span>
                    </div>
                    <div class="bt-metric-content">
                        <h3><?php _e('Page Load Time', 'bt-site-recommendations'); ?></h3>
                        <p class="bt-metric-value" id="bt-load-time">--</p>
                        <p class="bt-metric-label"><?php _e('milliseconds', 'bt-site-recommendations'); ?></p>
                    </div>
                </div>

                <div class="bt-metric-card">
                    <div class="bt-metric-icon">
                        <span class="dashicons dashicons-text-page"></span>
                    </div>
                    <div class="bt-metric-content">
                        <h3><?php _e('Page Size', 'bt-site-recommendations'); ?></h3>
                        <p class="bt-metric-value" id="bt-page-size">--</p>
                        <p class="bt-metric-label"><?php _e('bytes', 'bt-site-recommendations'); ?></p>
                    </div>
                </div>

                <div class="bt-metric-card">
                    <div class="bt-metric-icon">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                    </div>
                    <div class="bt-metric-content">
                        <h3><?php _e('SEO Score', 'bt-site-recommendations'); ?></h3>
                        <p class="bt-metric-value" id="bt-seo-score">--</p>
                        <p class="bt-metric-label"><?php _e('out of 100', 'bt-site-recommendations'); ?></p>
                    </div>
                </div>

                <div class="bt-metric-card">
                    <div class="bt-metric-icon">
                        <span class="dashicons dashicons-warning"></span>
                    </div>
                    <div class="bt-metric-content">
                        <h3><?php _e('Issues Found', 'bt-site-recommendations'); ?></h3>
                        <p class="bt-metric-value" id="bt-issues-count">--</p>
                        <p class="bt-metric-label"><?php _e('recommendations', 'bt-site-recommendations'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Recommendations Tabs -->
            <div class="bt-tabs">
                <nav class="bt-tabs-nav">
                    <button class="bt-tab-button active" data-tab="all">
                        <?php _e('All Recommendations', 'bt-site-recommendations'); ?>
                    </button>
                    <button class="bt-tab-button" data-tab="page-speed">
                        <?php _e('Page Speed', 'bt-site-recommendations'); ?>
                    </button>
                    <button class="bt-tab-button" data-tab="seo">
                        <?php _e('SEO', 'bt-site-recommendations'); ?>
                    </button>
                </nav>

                <div class="bt-tabs-content">
                    <div id="bt-tab-all" class="bt-tab-pane active">
                        <div id="bt-recommendations-all"></div>
                    </div>
                    <div id="bt-tab-page-speed" class="bt-tab-pane">
                        <div id="bt-recommendations-speed"></div>
                    </div>
                    <div id="bt-tab-seo" class="bt-tab-pane">
                        <div id="bt-recommendations-seo"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="bt-error" style="display: none;">
            <div class="notice notice-error">
                <p id="bt-error-message"></p>
            </div>
        </div>
    </div>
</div>
