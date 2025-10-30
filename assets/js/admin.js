/**
 * Admin JavaScript for BT Site Recommendations
 */

(function($) {
    'use strict';
    
    const btSR = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            // Settings
            $('#bt-sr-save-settings').on('click', this.saveSettings);
            $('#bt-sr-clear-cache').on('click', this.clearCache);
            $('.bt-sr-test-connection').on('click', this.testConnection);
            
            // Code Analysis
            $('#bt-sr-run-code-analysis').on('click', function() {
                btSR.runAnalysis('code', false);
            });
            $('#bt-sr-force-code-analysis').on('click', function() {
                btSR.runAnalysis('code', true);
            });
            $('#bt-sr-download-code-report').on('click', function() {
                btSR.downloadReport('code');
            });
            $('#bt-sr-copy-code-report').on('click', function() {
                btSR.copyReport('code');
            });
            
            // Database Analysis
            $('#bt-sr-run-db-analysis').on('click', function() {
                btSR.runAnalysis('database', false);
            });
            $('#bt-sr-force-db-analysis').on('click', function() {
                btSR.runAnalysis('database', true);
            });
            $('#bt-sr-apply-safe-fixes').on('click', this.applySafeFixes);
            $('#bt-sr-download-db-report').on('click', function() {
                btSR.downloadReport('database');
            });
            
            // Image Analysis
            $('#bt-sr-scan-images').on('click', function() {
                btSR.runAnalysis('images', false);
            });
            $('#bt-sr-force-image-scan').on('click', function() {
                btSR.runAnalysis('images', true);
            });
            $('.bt-sr-batch-convert-webp').on('click', function() {
                btSR.batchOptimize('convert_webp', '.webp-checkbox:checked');
            });
            $('.bt-sr-batch-add-alt').on('click', function() {
                btSR.batchOptimize('add_alt_text', '.alt-checkbox:checked');
            });
            $('.bt-sr-select-all-webp').on('click', function() {
                $('.webp-checkbox').prop('checked', true);
            });
            $('.bt-sr-select-all-alt').on('click', function() {
                $('.alt-checkbox').prop('checked', true);
            });
        },
        
        saveSettings: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $message = $('#bt-sr-save-message');
            
            // Collect settings
            const settings = {
                default_ai_provider: $('#default_ai_provider').val(),
                openai_api_key: $('#openai_api_key').val(),
                anthropic_api_key: $('#anthropic_api_key').val(),
                hosting_override: $('#hosting_override').val(),
                permissions: {}
            };
            
            // Collect permissions
            $('input[name^="permissions"]').each(function() {
                const name = $(this).attr('name').match(/\[([^\]]+)\]/)[1];
                settings.permissions[name] = $(this).is(':checked');
            });
            
            $button.prop('disabled', true).text(btSiteRecommendations.strings.saving);
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'bt_sr_save_settings',
                    nonce: btSiteRecommendations.nonce,
                    settings: settings
                },
                success: function(response) {
                    if (response.success) {
                        $message.html('<div class="notice notice-success"><p>' + btSiteRecommendations.strings.saved + '</p></div>');
                    } else {
                        $message.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                    setTimeout(function() {
                        $message.empty();
                    }, 3000);
                },
                error: function() {
                    $message.html('<div class="notice notice-error"><p>' + btSiteRecommendations.strings.error + '</p></div>');
                },
                complete: function() {
                    $button.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save Settings');
                }
            });
        },
        
        clearCache: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            if (!confirm('Are you sure you want to clear all cached analysis results?')) {
                return;
            }
            
            $button.prop('disabled', true);
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'bt_sr_clear_cache',
                    nonce: btSiteRecommendations.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Cache cleared successfully.');
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },
        
        testConnection: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const provider = $button.data('provider');
            const apiKey = $('#' + provider + '_api_key').val();
            const $status = $('.bt-sr-connection-status[data-provider="' + provider + '"]');
            
            if (!apiKey) {
                $status.html('<span class="error">Please enter an API key first.</span>');
                return;
            }
            
            $button.prop('disabled', true).text(btSiteRecommendations.strings.testing);
            $status.html('<span class="testing">Testing...</span>');
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'bt_sr_test_ai_connection',
                    nonce: btSiteRecommendations.nonce,
                    provider: provider,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('<span class="success"><span class="dashicons dashicons-yes-alt"></span> ' + btSiteRecommendations.strings.connected + '</span>');
                    } else {
                        $status.html('<span class="error"><span class="dashicons dashicons-dismiss"></span> ' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $status.html('<span class="error">' + btSiteRecommendations.strings.connectionFailed + '</span>');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Test Connection');
                }
            });
        },
        
        runAnalysis: function(type, force) {
            const config = {
                code: {
                    action: 'bt_sr_analyze_code',
                    progress: '#bt-sr-code-progress',
                    results: '#bt-sr-code-results',
                    button: '#bt-sr-run-code-analysis'
                },
                database: {
                    action: 'bt_sr_analyze_database',
                    progress: '#bt-sr-db-progress',
                    results: '#bt-sr-db-results',
                    button: '#bt-sr-run-db-analysis'
                },
                images: {
                    action: 'bt_sr_analyze_images',
                    progress: '#bt-sr-image-progress',
                    results: '#bt-sr-image-results',
                    button: '#bt-sr-scan-images'
                }
            };
            
            const cfg = config[type];
            if (!cfg) return;
            
            $(cfg.button).prop('disabled', true);
            $(cfg.progress).show();
            $(cfg.results).hide();
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: cfg.action,
                    nonce: btSiteRecommendations.nonce,
                    force: force ? 'true' : 'false'
                },
                success: function(response) {
                    if (response.success) {
                        btSR.displayResults(type, response.data);
                        $(cfg.results).show();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert(btSiteRecommendations.strings.error);
                },
                complete: function() {
                    $(cfg.button).prop('disabled', false);
                    $(cfg.progress).hide();
                }
            });
        },
        
        displayResults: function(type, data) {
            if (type === 'code') {
                this.displayCodeResults(data);
            } else if (type === 'database') {
                this.displayDatabaseResults(data);
            } else if (type === 'images') {
                this.displayImageResults(data);
            }
        },
        
        displayCodeResults: function(data) {
            // Security Issues
            if (data.security_issues && data.security_issues.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>Severity</th><th>File</th><th>Line</th><th>Issue</th><th>Fix</th></tr></thead><tbody>';
                data.security_issues.forEach(function(issue) {
                    const severity = issue.severity || 'medium';
                    html += '<tr class="severity-' + severity + '">';
                    html += '<td><span class="severity-badge severity-' + severity + '">' + severity + '</span></td>';
                    html += '<td>' + issue.file + '</td>';
                    html += '<td>' + (issue.line || 'N/A') + '</td>';
                    html += '<td>' + issue.issue + '</td>';
                    html += '<td>' + issue.fix + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-security-issues').html(html);
            } else {
                $('#bt-sr-security-issues').html('<p class="no-issues">No security issues found. Great job!</p>');
            }
            
            // Page Speed Score
            if (data.page_speed_score) {
                let html = '<div class="bt-sr-page-speed-summary">';
                html += '<span class="dashicons dashicons-performance"></span> ';
                html += '<strong>Core Web Vitals Impact:</strong> ' + data.page_speed_score;
                html += '</div>';
                $('#bt-sr-page-speed-score').html(html);
            }
            
            // Deprecated Functions
            if (data.deprecated_functions && data.deprecated_functions.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>File</th><th>Line</th><th>Function</th><th>Replacement</th></tr></thead><tbody>';
                data.deprecated_functions.forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + item.file + '</td>';
                    html += '<td>' + (item.line || 'N/A') + '</td>';
                    html += '<td><code>' + item.function + '</code></td>';
                    html += '<td><code>' + item.replacement + '</code></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-deprecated-functions').html(html);
            } else {
                $('#bt-sr-deprecated-functions').html('<p class="no-issues">No deprecated functions found.</p>');
            }
            
            // Performance Issues (Page Speed)
            if (data.performance_issues && data.performance_issues.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.performance_issues.forEach(function(issue) {
                    html += '<li>';
                    html += '<strong>' + issue.file + '</strong> (Line ' + (issue.line || 'N/A') + ')<br>';
                    html += '<span class="issue-text">' + issue.issue + '</span><br>';
                    if (issue.impact) {
                        html += '<span class="page-speed-impact"><span class="dashicons dashicons-performance"></span> ' + issue.impact + '</span><br>';
                    }
                    html += '<em>Fix: ' + issue.fix + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-performance-issues').html(html);
            } else {
                $('#bt-sr-performance-issues').html('<p class="no-issues">No performance issues found. Great for page speed!</p>');
            }
            
            // SEO Issues
            if (data.seo_issues && data.seo_issues.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.seo_issues.forEach(function(issue) {
                    html += '<li>';
                    html += '<strong>' + issue.file + '</strong> (Line ' + (issue.line || 'N/A') + ')<br>';
                    html += '<span class="issue-text">' + issue.issue + '</span><br>';
                    if (issue.seo_impact) {
                        html += '<span class="seo-impact"><span class="dashicons dashicons-search"></span> ' + issue.seo_impact + '</span><br>';
                    }
                    html += '<em>Fix: ' + issue.fix + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-seo-issues').html(html);
            } else {
                $('#bt-sr-seo-issues').html('<p class="no-issues">No SEO issues found in code.</p>');
            }
            
            // Recommended Plugins
            if (data.recommended_plugins && data.recommended_plugins.length > 0) {
                let html = '<div class="bt-sr-plugins-grid">';
                data.recommended_plugins.forEach(function(plugin) {
                    html += '<div class="bt-sr-plugin-card">';
                    html += '<h4>' + plugin.name + '</h4>';
                    html += '<p><strong>Purpose:</strong> ' + plugin.purpose + '</p>';
                    html += '<p><strong>Benefit:</strong> ' + plugin.benefit + '</p>';
                    if (plugin.url) {
                        html += '<a href="' + plugin.url + '" target="_blank" class="button button-small">View Plugin</a>';
                    }
                    html += '</div>';
                });
                html += '</div>';
                $('#bt-sr-recommended-plugins').html(html);
            } else {
                $('#bt-sr-recommended-plugins').html('<p class="no-issues">No additional plugins recommended.</p>');
            }
            
            // Code Quality
            if (data.code_quality && data.code_quality.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.code_quality.forEach(function(issue) {
                    html += '<li>';
                    html += '<strong>' + issue.file + '</strong> (Line ' + (issue.line || 'N/A') + ')<br>';
                    html += issue.issue + '<br>';
                    html += '<em>Fix: ' + issue.fix + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-code-quality').html(html);
            } else {
                $('#bt-sr-code-quality').html('<p class="no-issues">Code quality looks good!</p>');
            }
            
            // Summary
            $('#bt-sr-code-summary').html('<p>' + (data.summary || 'Analysis complete.') + '</p>');
        },
        
        displayDatabaseResults: function(data) {
            // Schema Optimization
            if (data.schema_optimization && data.schema_optimization.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>Severity</th><th>Table</th><th>Issue</th><th>Recommendation</th></tr></thead><tbody>';
                data.schema_optimization.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><span class="severity-badge severity-' + item.severity + '">' + item.severity + '</span></td>';
                    html += '<td>' + item.table + '</td>';
                    html += '<td>' + item.issue + '</td>';
                    html += '<td>' + item.recommendation + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-schema-optimization').html(html);
            } else {
                $('#bt-sr-schema-optimization').html('<p class="no-issues">Database schema is optimized.</p>');
            }
            
            // Query Performance
            if (data.query_performance && data.query_performance.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.query_performance.forEach(function(item) {
                    html += '<li>';
                    html += '<strong>Query:</strong> <code>' + item.query + '</code><br>';
                    html += item.issue + '<br>';
                    html += '<em>' + item.recommendation + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-query-performance').html(html);
            } else {
                $('#bt-sr-query-performance').html('<p class="no-issues">No slow queries detected.</p>');
            }
            
            // Data Cleanup
            if (data.data_cleanup && data.data_cleanup.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.data_cleanup.forEach(function(item) {
                    html += '<li>';
                    html += '<strong>' + item.type + '</strong><br>';
                    html += 'Items: ' + item.items.join(', ') + '<br>';
                    html += '<em>' + item.recommendation + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-data-cleanup').html(html);
            } else {
                $('#bt-sr-data-cleanup').html('<p class="no-issues">No cleanup needed.</p>');
            }
            
            // Security Issues
            if (data.security_issues && data.security_issues.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>Severity</th><th>Issue</th><th>Recommendation</th></tr></thead><tbody>';
                data.security_issues.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><span class="severity-badge severity-' + item.severity + '">' + item.severity + '</span></td>';
                    html += '<td>' + item.issue + '</td>';
                    html += '<td>' + item.recommendation + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-security-checks').html(html);
            } else {
                $('#bt-sr-security-checks').html('<p class="no-issues">No security issues found.</p>');
            }
            
            // Data Integrity
            if (data.data_integrity && data.data_integrity.length > 0) {
                let html = '<ul class="bt-sr-issue-list">';
                data.data_integrity.forEach(function(item) {
                    html += '<li>';
                    html += '<strong>' + item.table + '</strong><br>';
                    html += item.issue + '<br>';
                    html += '<em>' + item.recommendation + '</em>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bt-sr-data-integrity').html(html);
            } else {
                $('#bt-sr-data-integrity').html('<p class="no-issues">Data integrity is good.</p>');
            }
            
            // Summary
            $('#bt-sr-db-summary').html('<p>' + (data.summary || 'Database analysis complete.') + '</p>');
        },
        
        displayImageResults: function(data) {
            // Statistics
            if (data.statistics) {
                $('#bt-sr-total-images').text(data.statistics.total_images || 0);
                $('#bt-sr-total-size').text(data.statistics.total_size || '0 B');
                $('#bt-sr-potential-savings').text(data.statistics.potential_savings || '0 B');
                $('#bt-sr-webp-candidates').text(data.statistics.webp_candidates || 0);
                $('#bt-sr-missing-alt').text(data.statistics.missing_alt_text || 0);
                $('#bt-sr-unused-images').text(data.statistics.unused_images || 0);
            }
            
            // Size Optimization
            if (data.size_optimization && data.size_optimization.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>Image</th><th>Current Size</th><th>Potential Savings</th><th>Recommendation</th></tr></thead><tbody>';
                data.size_optimization.forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + item.filename + '</td>';
                    html += '<td>' + item.current_size + '</td>';
                    html += '<td>' + item.potential_savings + '</td>';
                    html += '<td>' + item.recommendation + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-size-optimization').html(html);
            } else {
                $('#bt-sr-size-optimization').html('<p class="no-issues">All images are optimally sized.</p>');
            }
            
            // Format Conversion
            if (data.format_conversion && data.format_conversion.length > 0) {
                let html = '<table class="widefat"><thead><tr><th><input type="checkbox" class="select-all-webp"></th><th>Image</th><th>Current Format</th><th>Recommended</th><th>Estimated Savings</th></tr></thead><tbody>';
                data.format_conversion.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><input type="checkbox" class="webp-checkbox" data-image-id="' + item.image_id + '"></td>';
                    html += '<td>' + item.filename + '</td>';
                    html += '<td>' + item.current_format.toUpperCase() + '</td>';
                    html += '<td>' + item.recommended_format.toUpperCase() + '</td>';
                    html += '<td>' + item.estimated_savings + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-format-conversion').html(html);
            } else {
                $('#bt-sr-format-conversion').html('<p class="no-issues">No format conversion needed.</p>');
            }
            
            // Missing Alt Text (SEO Focus)
            if (data.missing_alt_text && data.missing_alt_text.length > 0) {
                let html = '<table class="widefat"><thead><tr><th><input type="checkbox" class="select-all-alt"></th><th>Image</th><th>Suggested Alt Text</th><th>SEO Impact</th></tr></thead><tbody>';
                data.missing_alt_text.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><input type="checkbox" class="alt-checkbox" data-image-id="' + item.image_id + '"></td>';
                    html += '<td>' + item.filename + '</td>';
                    html += '<td>' + item.suggested_alt_text + '</td>';
                    html += '<td>' + (item.seo_impact || 'Improves image SEO and accessibility') + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-missing-alt-text').html(html);
            } else {
                $('#bt-sr-missing-alt-text').html('<p class="no-issues">All images have alt text. Great for SEO!</p>');
            }
            
            // Lazy Loading
            if (data.lazy_loading) {
                let html = '<div class="bt-sr-lazy-loading-status">';
                if (data.lazy_loading.implemented) {
                    html += '<p class="no-issues"><span class="dashicons dashicons-yes-alt"></span> Lazy loading is implemented.</p>';
                } else {
                    html += '<div class="notice notice-warning inline"><p><strong>Lazy Loading Not Detected</strong></p>';
                    html += '<p>' + (data.lazy_loading.recommendation || 'Consider implementing lazy loading to improve initial page load speed.') + '</p></div>';
                }
                html += '</div>';
                $('#bt-sr-lazy-loading').html(html);
            }
            
            // Page Speed & SEO Improvements
            if (data.page_speed_improvement || data.seo_improvement) {
                let html = '<div class="bt-sr-improvement-summary">';
                if (data.page_speed_improvement) {
                    html += '<div class="improvement-item">';
                    html += '<span class="dashicons dashicons-performance"></span> ';
                    html += '<strong>Page Speed:</strong> ' + data.page_speed_improvement;
                    html += '</div>';
                }
                if (data.seo_improvement) {
                    html += '<div class="improvement-item">';
                    html += '<span class="dashicons dashicons-search"></span> ';
                    html += '<strong>SEO:</strong> ' + data.seo_improvement;
                    html += '</div>';
                }
                html += '</div>';
                $('#bt-sr-image-improvements').html(html);
            }
            
            // Recommended Image Plugins
            if (data.recommended_plugins && data.recommended_plugins.length > 0) {
                let html = '<div class="bt-sr-plugins-grid">';
                data.recommended_plugins.forEach(function(plugin) {
                    html += '<div class="bt-sr-plugin-card">';
                    html += '<h4>' + plugin.name + '</h4>';
                    html += '<p><strong>Purpose:</strong> ' + plugin.purpose + '</p>';
                    html += '<p><strong>Benefit:</strong> ' + plugin.benefit + '</p>';
                    if (plugin.url) {
                        html += '<a href="' + plugin.url + '" target="_blank" class="button button-small">View Plugin</a>';
                    }
                    html += '</div>';
                });
                html += '</div>';
                $('#bt-sr-image-recommended-plugins').html(html);
            }
            
            // Unused Images
            if (data.unused_images && data.unused_images.length > 0) {
                let html = '<table class="widefat"><thead><tr><th>Image</th><th>Last Used</th><th>Can Remove</th></tr></thead><tbody>';
                data.unused_images.forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + item.filename + '</td>';
                    html += '<td>' + item.last_used + '</td>';
                    html += '<td>' + (item.can_remove ? 'Yes' : 'No') + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#bt-sr-unused-images-list').html(html);
            } else {
                $('#bt-sr-unused-images-list').html('<p class="no-issues">No unused images found.</p>');
            }
            
            // Summary
            $('#bt-sr-image-summary').html('<p>' + (data.summary || 'Image analysis complete.') + '</p>');
        },
        
        batchOptimize: function(action, selector) {
            const imageIds = [];
            $(selector).each(function() {
                imageIds.push($(this).data('image-id'));
            });
            
            if (imageIds.length === 0) {
                alert('Please select at least one image.');
                return;
            }
            
            if (!confirm('Process ' + imageIds.length + ' image(s)?')) {
                return;
            }
            
            $('#bt-sr-batch-progress').show();
            $('.bt-sr-progress-text').text('Processing ' + imageIds.length + ' images...');
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'bt_sr_optimize_images',
                    nonce: btSiteRecommendations.nonce,
                    image_ids: imageIds,
                    action_type: action
                },
                success: function(response) {
                    if (response.success) {
                        let html = '<div class="notice notice-success"><p>' + response.data.message + '</p></div>';
                        $('#bt-sr-batch-results').html(html);
                        // Refresh the analysis
                        setTimeout(function() {
                            btSR.runAnalysis('images', true);
                        }, 2000);
                    } else {
                        $('#bt-sr-batch-results').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#bt-sr-batch-results').html('<div class="notice notice-error"><p>' + btSiteRecommendations.strings.error + '</p></div>');
                },
                complete: function() {
                    setTimeout(function() {
                        $('#bt-sr-batch-progress').hide();
                        $('#bt-sr-batch-results').empty();
                    }, 3000);
                }
            });
        },
        
        applySafeFixes: function(e) {
            e.preventDefault();
            
            if (!confirm('Apply safe database fixes? This will delete expired transients, orphaned metadata, and optimize tables.')) {
                return;
            }
            
            const fixes = [
                {type: 'delete_transients'},
                {type: 'delete_orphaned_postmeta'},
                {type: 'optimize_tables'}
            ];
            
            $.ajax({
                url: btSiteRecommendations.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'bt_sr_apply_db_fixes',
                    nonce: btSiteRecommendations.nonce,
                    fixes: fixes
                },
                success: function(response) {
                    if (response.success) {
                        alert('Safe fixes applied successfully.');
                        btSR.runAnalysis('database', true);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert(btSiteRecommendations.strings.error);
                }
            });
        },
        
        downloadReport: function(type) {
            alert('Report download feature coming soon!');
        },
        
        copyReport: function(type) {
            alert('Copy to clipboard feature coming soon!');
        }
    };
    
    $(document).ready(function() {
        btSR.init();
    });
    
})(jQuery);

