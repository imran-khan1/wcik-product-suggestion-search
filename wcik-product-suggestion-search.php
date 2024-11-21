<?php
/**
 * Plugin Name: WCIK Product Suggestion Search
 * Description: AJAX-based product suggestion search plugin with custom filters for WooCommerce.
 * Version: 1.0.0
 * Author: Imran Khan
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

register_activation_hook( __FILE__, 'wcik_check_woocommerce_on_activation' );

function wcik_check_woocommerce_on_activation() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        wp_die(
            'This plugin requires WooCommerce to be installed and active. Please install WooCommerce and try again.',
            'Plugin Activation Error',
            [ 'back_link' => true ]
        );
    }
}


class WCIK_Product_Suggestion_Search {
    
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants() {
        define( 'WCIK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'WCIK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    }

    private function includes() {
        require_once WCIK_PLUGIN_PATH . 'includes/class-wcik-settings.php';
        require_once WCIK_PLUGIN_PATH . 'includes/class-wcik-search-handler.php';
        require_once WCIK_PLUGIN_PATH . 'includes/class-wcik-ajax-handler.php';
    }

    private function init_hooks() {
        add_action( 'admin_menu', [ 'WCIK_Settings', 'register_settings_page' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_shortcode( 'wcik_search_form', [ 'WCIK_Search_Handler', 'render_search_form' ] );
        add_action( 'wp_ajax_wcik_search_products', [ 'WCIK_Ajax_Handler', 'handle_ajax_request' ] );
        add_action( 'wp_ajax_nopriv_wcik_search_products', [ 'WCIK_Ajax_Handler', 'handle_ajax_request' ] );
        add_action( 'wp_ajax_wcik_fetch_suggestions', [ 'WCIK_Ajax_Handler', 'fetch_suggestions' ] );
        add_action( 'wp_ajax_nopriv_wcik_fetch_suggestions', [ 'WCIK_Ajax_Handler', 'fetch_suggestions' ] );

    }

    public function enqueue_assets() {
        wp_enqueue_style( 'wcik-search-form', WCIK_PLUGIN_URL . 'assets/css/search-form.css' );
        wp_enqueue_script( 'wcik-search-form', WCIK_PLUGIN_URL . 'assets/js/search-form.js', [ 'jquery' ], null, true );
        // wp_localize_script( 'wcik-search-form', 'wcikAjax', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
        wp_localize_script( 'wcik-search-form', 'wcikAjax', [
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'wcik_search_nonce' ),
        ]);
    }
}

new WCIK_Product_Suggestion_Search();
