/**
 * BT Site Recommendations Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Tab switching
        $('.bt-tab-button').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.bt-tab-button').removeClass('active');
            $(this).addClass('active');
            
            $('.bt-tab-pane').removeClass('active');
            $('#bt-tab-' + tab).addClass('active');
        });

        // Analyze button click
        $('#bt-analyze-btn').on('click', function() {
            const $button = $(this);
            $button.prop('disabled', true);
            
            $('#bt-results').hide();
            $('#bt-error').hide();
            $('#bt-loading').show();

            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bt_analyze_site',
                    nonce: btSiteRecommendations.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayResults(response.data);
                    } else {
                        showError(response.data.message || 'An error occurred during analysis.');
                    }
                },
                error: function(xhr, status, error) {
                    showError('Failed to analyze site. Please try again.');
                },
                complete: function() {
                    $('#bt-loading').hide();
                    $button.prop('disabled', false);
                }
            });
        });

        function displayResults(data) {
            // Update metrics
            if (data.page_speed && data.page_speed.load_time) {
                $('#bt-load-time').text(data.page_speed.load_time);
            }
            
            if (data.page_speed && data.page_speed.page_size) {
                $('#bt-page-size').text(formatBytes(data.page_speed.page_size));
            }

            // Calculate SEO score
            const seoScore = calculateSeoScore(data.seo);
            $('#bt-seo-score').text(seoScore);

            // Count total recommendations
            const totalRecommendations = (data.recommendations.page_speed || []).length + 
                                        (data.recommendations.seo || []).length;
            $('#bt-issues-count').text(totalRecommendations);

            // Update analysis date
            const now = new Date();
            $('#bt-analysis-date').text(now.toLocaleString());

            // Display recommendations
            displayRecommendations(data.recommendations);

            // Show results
            $('#bt-results').show();

            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#bt-results').offset().top - 50
            }, 500);
        }

        function displayRecommendations(recommendations) {
            const allRecommendations = [
                ...(recommendations.page_speed || []),
                ...(recommendations.seo || [])
            ];

            // Sort by priority
            const priorityOrder = { 'critical': 0, 'high': 1, 'medium': 2, 'low': 3 };
            allRecommendations.sort((a, b) => {
                return (priorityOrder[a.priority] || 99) - (priorityOrder[b.priority] || 99);
            });

            // Display all recommendations
            $('#bt-recommendations-all').html(
                allRecommendations.length > 0
                    ? allRecommendations.map(rec => renderRecommendation(rec)).join('')
                    : renderEmptyState('All categories look good! No major issues found.')
            );

            // Display Page Speed recommendations
            $('#bt-recommendations-speed').html(
                (recommendations.page_speed && recommendations.page_speed.length > 0)
                    ? recommendations.page_speed.map(rec => renderRecommendation(rec)).join('')
                    : renderEmptyState('Page Speed looks good! No issues found.')
            );

            // Display SEO recommendations
            $('#bt-recommendations-seo').html(
                (recommendations.seo && recommendations.seo.length > 0)
                    ? recommendations.seo.map(rec => renderRecommendation(rec)).join('')
                    : renderEmptyState('SEO looks good! No issues found.')
            );
        }

        function renderRecommendation(rec) {
            const actionsHtml = rec.actions && rec.actions.length > 0
                ? `<div class="bt-recommendation-actions">
                       <h4>Recommended Actions:</h4>
                       <ul>
                           ${rec.actions.map(action => `<li>${escapeHtml(action)}</li>`).join('')}
                       </ul>
                   </div>`
                : '';

            return `
                <div class="bt-recommendation priority-${rec.priority}">
                    <div class="bt-recommendation-header">
                        <div class="bt-recommendation-title">
                            <h3>${escapeHtml(rec.title)}</h3>
                            <span class="bt-priority-badge priority-${rec.priority}">${rec.priority}</span>
                        </div>
                        <span class="bt-recommendation-category">${escapeHtml(rec.category)}</span>
                    </div>
                    <div class="bt-recommendation-description">
                        ${escapeHtml(rec.description)}
                    </div>
                    ${actionsHtml}
                </div>
            `;
        }

        function renderEmptyState(message) {
            return `
                <div class="bt-empty-state">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <p>${escapeHtml(message)}</p>
                </div>
            `;
        }

        function showError(message) {
            $('#bt-error-message').text(message);
            $('#bt-error').show();
        }

        function calculateSeoScore(seoData) {
            if (!seoData || seoData.error) {
                return 0;
            }

            let score = 100;

            // Deduct points for missing elements
            if (!seoData.has_title) score -= 20;
            else if (seoData.title_length > 60 || seoData.title_length < 30) score -= 5;

            if (!seoData.has_meta_description) score -= 15;
            else if (seoData.meta_description_length > 160) score -= 5;

            if (seoData.h1_count === 0) score -= 10;
            else if (seoData.h1_count > 1) score -= 5;

            if (seoData.images_without_alt > 0) {
                const altPenalty = Math.min(15, (seoData.images_without_alt / seoData.total_images) * 15);
                score -= altPenalty;
            }

            if (!seoData.has_canonical) score -= 5;
            if (!seoData.has_og_tags) score -= 5;
            if (!seoData.has_schema) score -= 5;

            return Math.max(0, Math.round(score));
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    });

})(jQuery);
