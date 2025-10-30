<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    BT_Site_Recommendations
 */
class BT_Site_Recommendations {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since    1.0.0
     * @access   protected
     * @var      BT_Site_Recommendations_Loader    $loader
     */
    protected $loader;

    /**
     * Initialize the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-loader.php';
        require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-admin.php';
        require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-analyzer.php';
        require_once BT_SITE_RECOMMENDATIONS_PATH . 'includes/class-bt-site-recommendations-ai.php';

        $this->loader = new BT_Site_Recommendations_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new BT_Site_Recommendations_Admin();

        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('wp_ajax_bt_analyze_site', $plugin_admin, 'ajax_analyze_site');
    }

    /**
     * Run the loader to execute all of the hooks.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }
}
