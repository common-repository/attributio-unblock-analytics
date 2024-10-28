<?php
/*
Plugin Name: Attributio Unblock Analytics
Plugin URI: https://www.attribut.io
Description: Detect whether Google Analytics or Tag Manager is being blocked and create a proxy for hits to be sent to Google Analytics. Get more accuration conversion goals and tracking abilities by creating a backup for your analytics!
Version: 0.1.3
Author: Attributio
Author URI: https://attribut.io/

    Copyright: © 2018 Attributio (email : hello@attribut.io)
    License: GNU General Public License v3.0
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Attributio' ) ) {

  define( 'ATTRIBUTIO_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
  define( 'ATTRIBUTIO_URL', plugin_dir_url( __FILE__ ) );

  class ATTRIBUTIO {
    public function __construct() {
      add_action( 'plugins_loaded', array( &$this, 'includes' ) );
      add_action( 'admin_menu', array( &$this, 'add_menu'), 11 );
      add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
      add_action( 'wp_enqueue_scripts', array( &$this,'scripts' ) );
      register_activation_hook( __FILE__, array( &$this, 'plugin_activation') );
      register_deactivation_hook( __FILE__, array( &$this, 'plugin_deactivation') );
      add_action( 'init', array( &$this, 'add_rewrite' ) );
      add_filter( 'request', array( &$this, 'rewrite_filter_request' ) );
    }

    public function includes() {
      require_once( 'includes/class-attributio-proxy.php' );
    }

    public function add_menu() { 
      if ( !isset($GLOBALS['admin_page_hooks']['attributio_settings']) ) {
        add_menu_page('Attributio Settings', 'Attributio', 'manage_options', 'attributio_settings', array(&$this, 'plugin_settings_page')); 
      }
    }

    public function plugin_settings_page() { 
      if(!current_user_can('manage_options')) { 
        wp_die(__('You do not have sufficient permissions to access this page.')); 
      }
      include(sprintf("%s/templates/settings.php", dirname(__FILE__))); 
    }

    public function plugin_action_links( $links, $file ) {
      if ( $file == plugin_basename( __FILE__ ) )
        $links[] = '<a href="admin.php?page=attributio_settings">' . __( 'Settings' , 'attributio_settings') . '</a>'; 

      return $links;
    } 

    public function scripts() {
      wp_enqueue_script( 'attributio-detect', ATTRIBUTIO_URL . 'assets/js/detect.js', array(), '1.3.7' );
      plugins_url( ATTRIBUTIO_URL . 'assets/js/ubga.js', __FILE__ );    
      $detect_params = array(
        'analytics_path' => ATTRIBUTIO_URL  . 'assets/js/ubga.js?ver=1.2',
        'analytics_proxy_path' => parse_url(site_url(), PHP_URL_PATH) . '/attributio-ubga',
        'analytics_property' => get_option('attributio_property'),
        'analytics_custom_hit' => stripcslashes(sanitize_text_field(get_option('attributio_custom_hit'))),
      );
      wp_localize_script( 'attributio-detect', 'attributio_params', $detect_params );
    }

    public function plugin_activation() {
      $this->add_rewrite();
      flush_rewrite_rules();
    }

    public function plugin_deactivation() {
      flush_rewrite_rules();
    }

    public function add_rewrite() { 
      add_rewrite_endpoint( 'attributio-ubga', EP_ROOT );
    }

    public function rewrite_filter_request( $vars ) {
      if( isset( $vars['attributio-ubga'] ) ) {
        $proxy = new Attributio_Proxy();
        $response = $proxy->ping_google(); 
        exit();
        // debug
        // wp_die('<pre>' . var_export($response, true) . '</pre>');
      } else {
        return $vars;
      }
    }

  }

  // finally instantiate our plugin class and add it to the set of globals
  $GLOBALS['attributio'] = new Attributio();
}