<?php
/**
 * Admin settings view.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$settings = get_option('bt_site_recommendations_settings', array());
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('bt_site_recommendations_settings');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="enable_page_speed">
                        <?php _e('Enable Page Speed Analysis', 'bt-site-recommendations'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" 
                           id="enable_page_speed" 
                           name="bt_site_recommendations_settings[enable_page_speed]" 
                           value="1" 
                           <?php checked(!empty($settings['enable_page_speed'])); ?> />
                    <p class="description">
                        <?php _e('Analyze page load times, compression, caching, and other performance metrics.', 'bt-site-recommendations'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="enable_seo">
                        <?php _e('Enable SEO Analysis', 'bt-site-recommendations'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" 
                           id="enable_seo" 
                           name="bt_site_recommendations_settings[enable_seo]" 
                           value="1" 
                           <?php checked(!empty($settings['enable_seo'])); ?> />
                    <p class="description">
                        <?php _e('Analyze meta tags, headings, images, and other SEO factors.', 'bt-site-recommendations'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="analysis_frequency">
                        <?php _e('Analysis Frequency', 'bt-site-recommendations'); ?>
                    </label>
                </th>
                <td>
                    <select id="analysis_frequency" name="bt_site_recommendations_settings[analysis_frequency]">
                        <option value="daily" <?php selected($settings['analysis_frequency'] ?? '', 'daily'); ?>>
                            <?php _e('Daily', 'bt-site-recommendations'); ?>
                        </option>
                        <option value="weekly" <?php selected($settings['analysis_frequency'] ?? 'weekly', 'weekly'); ?>>
                            <?php _e('Weekly', 'bt-site-recommendations'); ?>
                        </option>
                        <option value="monthly" <?php selected($settings['analysis_frequency'] ?? '', 'monthly'); ?>>
                            <?php _e('Monthly', 'bt-site-recommendations'); ?>
                        </option>
                        <option value="manual" <?php selected($settings['analysis_frequency'] ?? '', 'manual'); ?>>
                            <?php _e('Manual Only', 'bt-site-recommendations'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php _e('How often should the plugin automatically analyze your site? (Coming soon - currently manual only)', 'bt-site-recommendations'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>
