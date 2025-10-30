<?php
/**
 * Admin Page Controller for BT Site Recommendations
 */

if (!defined('ABSPATH')) {
    exit;
}

class BT_Site_Recommendations_Admin_Page {
    
    private $current_tab = 'welcome';
    
    public function __construct() {
        $this->current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'welcome';
    }
    
    /**
     * Render the main admin page
     */
    public function render() {
        ?>
        <div class="wrap bt-site-recommendations-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="bt-sr-tabs-container">
                <?php $this->render_tabs(); ?>
                
                <div class="bt-sr-tab-content">
                    <?php $this->render_current_tab(); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render tab navigation
     */
    private function render_tabs() {
        $tabs = array(
            'welcome' => array(
                'title' => __('Welcome', 'bt-site-recommendations'),
                'icon' => 'dashicons-admin-home',
            ),
            'code' => array(
                'title' => __('Code Analysis', 'bt-site-recommendations'),
                'icon' => 'dashicons-editor-code',
            ),
            'database' => array(
                'title' => __('Database', 'bt-site-recommendations'),
                'icon' => 'dashicons-database',
            ),
            'images' => array(
                'title' => __('Images', 'bt-site-recommendations'),
                'icon' => 'dashicons-format-image',
            ),
        );
        
        ?>
        <nav class="nav-tab-wrapper wp-clearfix">
            <?php foreach ($tabs as $tab_key => $tab) : ?>
                <?php
                $active_class = ($this->current_tab === $tab_key) ? 'nav-tab-active' : '';
                $tab_url = add_query_arg(array(
                    'page' => 'bt-site-recommendations',
                    'tab' => $tab_key,
                ), admin_url('admin.php'));
                ?>
                <a href="<?php echo esc_url($tab_url); ?>" 
                   class="nav-tab <?php echo esc_attr($active_class); ?>"
                   data-tab="<?php echo esc_attr($tab_key); ?>">
                    <span class="dashicons <?php echo esc_attr($tab['icon']); ?>"></span>
                    <?php echo esc_html($tab['title']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
    }
    
    /**
     * Render current tab content
     */
    private function render_current_tab() {
        $tab_file = BT_SITE_RECOMMENDATIONS_PLUGIN_PATH . 'admin/views/' . $this->current_tab . '-tab.php';
        
        if (file_exists($tab_file)) {
            include $tab_file;
        } else {
            echo '<p>' . esc_html__('Tab content not found.', 'bt-site-recommendations') . '</p>';
        }
    }
    
    /**
     * Get hosting detector instance
     */
    public function get_hosting_detector() {
        return new BT_Site_Recommendations_Hosting_Detector();
    }
    
    /**
     * Get settings instance
     */
    public function get_settings() {
        return BT_Site_Recommendations_Settings::get_all();
    }
}

