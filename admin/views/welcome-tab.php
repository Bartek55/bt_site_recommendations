<?php
/**
 * Welcome Tab View
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = BT_Site_Recommendations_Settings::get_all();
$hosting_detector = new BT_Site_Recommendations_Hosting_Detector();
$hosting_info = $hosting_detector->detect();
$hosting_capabilities = $hosting_detector->get_hosting_capabilities();
?>

<div class="bt-sr-welcome-tab">
    
    <!-- Plugin Introduction -->
    <div class="bt-sr-card">
        <h2><?php _e('Welcome to Site Recommendations', 'bt-site-recommendations'); ?></h2>
        <p><?php _e('This plugin uses advanced AI to analyze your WordPress site and provide actionable recommendations for:', 'bt-site-recommendations'); ?></p>
        <ul class="bt-sr-features-list">
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e('Code Security & Performance', 'bt-site-recommendations'); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e('Database Optimization', 'bt-site-recommendations'); ?></li>
            <li><span class="dashicons dashicons-yes-alt"></span> <?php _e('Image Optimization', 'bt-site-recommendations'); ?></li>
        </ul>
        <p><strong><?php _e('Get started by configuring your AI provider and permissions below.', 'bt-site-recommendations'); ?></strong></p>
    </div>
    
    <!-- AI Provider Configuration -->
    <div class="bt-sr-card">
        <h2><?php _e('AI Provider Configuration', 'bt-site-recommendations'); ?></h2>
        
        <form id="bt-sr-settings-form">
            <?php wp_nonce_field('bt_site_recommendations_save_settings', 'bt_sr_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="default_ai_provider"><?php _e('Default AI Provider', 'bt-site-recommendations'); ?></label>
                    </th>
                    <td>
                        <select id="default_ai_provider" name="default_ai_provider">
                            <option value=""><?php _e('-- Select Provider --', 'bt-site-recommendations'); ?></option>
                            <option value="openai" <?php selected($settings['ai_provider'], 'openai'); ?>><?php _e('OpenAI (GPT-5)', 'bt-site-recommendations'); ?></option>
                            <option value="anthropic" <?php selected($settings['ai_provider'], 'anthropic'); ?>><?php _e('Anthropic (Claude Sonnet 4.5)', 'bt-site-recommendations'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select which AI model to use for analysis.', 'bt-site-recommendations'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="openai_api_key"><?php _e('OpenAI API Key', 'bt-site-recommendations'); ?></label>
                    </th>
                    <td>
                        <input type="password" 
                               id="openai_api_key" 
                               name="openai_api_key" 
                               value="<?php echo esc_attr($settings['api_keys']['openai']); ?>" 
                               class="regular-text" 
                               placeholder="sk-..." />
                        <button type="button" class="button bt-sr-test-connection" data-provider="openai">
                            <?php _e('Test Connection', 'bt-site-recommendations'); ?>
                        </button>
                        <p class="description">
                            <?php _e('Get your API key from:', 'bt-site-recommendations'); ?> 
                            <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>
                        </p>
                        <div class="bt-sr-connection-status" data-provider="openai"></div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="anthropic_api_key"><?php _e('Anthropic API Key', 'bt-site-recommendations'); ?></label>
                    </th>
                    <td>
                        <input type="password" 
                               id="anthropic_api_key" 
                               name="anthropic_api_key" 
                               value="<?php echo esc_attr($settings['api_keys']['anthropic']); ?>" 
                               class="regular-text" 
                               placeholder="sk-ant-..." />
                        <button type="button" class="button bt-sr-test-connection" data-provider="anthropic">
                            <?php _e('Test Connection', 'bt-site-recommendations'); ?>
                        </button>
                        <p class="description">
                            <?php _e('Get your API key from:', 'bt-site-recommendations'); ?> 
                            <a href="https://console.anthropic.com/" target="_blank">https://console.anthropic.com/</a>
                        </p>
                        <div class="bt-sr-connection-status" data-provider="anthropic"></div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    
    <!-- Permissions -->
    <div class="bt-sr-card">
        <h2><?php _e('Analysis Permissions', 'bt-site-recommendations'); ?></h2>
        <p><?php _e('Control what data the AI can access for analysis. All permissions are enabled by default for comprehensive recommendations.', 'bt-site-recommendations'); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Code Analysis', 'bt-site-recommendations'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[read_theme_files]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['read_theme_files'], true); ?>>
                            <?php _e('Read active theme files', 'bt-site-recommendations'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[read_plugin_files]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['read_plugin_files'], true); ?>>
                            <?php _e('Read active plugin files', 'bt-site-recommendations'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[read_wp_config]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['read_wp_config'], true); ?>>
                            <?php _e('Read wp-config.php (for configuration analysis)', 'bt-site-recommendations'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Database Analysis', 'bt-site-recommendations'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[access_db_structure]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['access_db_structure'], true); ?>>
                            <?php _e('Access database structure (tables, indexes)', 'bt-site-recommendations'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[query_db_content]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['query_db_content'], true); ?>>
                            <?php _e('Query database content (for data analysis)', 'bt-site-recommendations'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Image Analysis', 'bt-site-recommendations'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[read_image_metadata]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['read_image_metadata'], true); ?>>
                            <?php _e('Read image metadata (size, dimensions, format)', 'bt-site-recommendations'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" 
                                   name="permissions[access_image_files]" 
                                   value="1" 
                                   <?php checked($settings['permissions']['access_image_files'], true); ?>>
                            <?php _e('Access and optimize image files', 'bt-site-recommendations'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Hosting Environment -->
    <div class="bt-sr-card">
        <h2><?php _e('Hosting Environment', 'bt-site-recommendations'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Detected Environment', 'bt-site-recommendations'); ?></th>
                <td>
                    <strong><?php echo esc_html($hosting_detector->get_hosting_display_name($hosting_info['type'])); ?></strong>
                    <?php if ($hosting_info['auto_detected']) : ?>
                        <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                        <span style="color: green;"><?php _e('Auto-detected', 'bt-site-recommendations'); ?></span>
                    <?php else : ?>
                        <span class="dashicons dashicons-admin-settings" style="color: orange;"></span>
                        <span style="color: orange;"><?php _e('Manually configured', 'bt-site-recommendations'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="hosting_override"><?php _e('Manual Override', 'bt-site-recommendations'); ?></label>
                </th>
                <td>
                    <select id="hosting_override" name="hosting_override">
                        <?php foreach ($hosting_detector->get_available_hosting_types() as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['hosting']['override'], $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Override auto-detection if needed.', 'bt-site-recommendations'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Capabilities', 'bt-site-recommendations'); ?></th>
                <td>
                    <ul class="bt-sr-capabilities-list">
                        <?php if ($hosting_capabilities['can_modify_files']) : ?>
                            <li><span class="dashicons dashicons-yes-alt" style="color: green;"></span> <?php _e('Can modify files', 'bt-site-recommendations'); ?></li>
                        <?php else : ?>
                            <li><span class="dashicons dashicons-dismiss" style="color: red;"></span> <?php _e('Cannot modify files (read-only filesystem)', 'bt-site-recommendations'); ?></li>
                        <?php endif; ?>
                        
                        <?php if ($hosting_capabilities['can_optimize_db']) : ?>
                            <li><span class="dashicons dashicons-yes-alt" style="color: green;"></span> <?php _e('Can optimize database', 'bt-site-recommendations'); ?></li>
                        <?php endif; ?>
                        
                        <?php if ($hosting_capabilities['has_object_cache']) : ?>
                            <li><span class="dashicons dashicons-yes-alt" style="color: green;"></span> <?php _e('Object caching available', 'bt-site-recommendations'); ?></li>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Save Button -->
    <div class="bt-sr-card">
        <button type="button" id="bt-sr-save-settings" class="button button-primary button-large">
            <span class="dashicons dashicons-saved"></span>
            <?php _e('Save Settings', 'bt-site-recommendations'); ?>
        </button>
        
        <button type="button" id="bt-sr-clear-cache" class="button button-secondary">
            <span class="dashicons dashicons-update"></span>
            <?php _e('Clear Analysis Cache', 'bt-site-recommendations'); ?>
        </button>
        
        <div id="bt-sr-save-message"></div>
    </div>
</div>

