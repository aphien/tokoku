<?php
/**
 * Plugin Name: TokoKu Bulk Product Editor
 * Plugin URI:  https://github.com/m-alfiandiismet/tokoku
 * Description: Plugin pengelolaan dan pengeditan masal produk lengkap untuk tema TokoKu. Memungkinkan pengaturan serentak harga normal, harga diskon/coret, ketersediaan stok (Tersedia, Habis, Pre-Order), kuantitas stok, kategori, label promo, berat, dan status puluhan hingga ribuan produk secara instan melalui Dashboard Interaktif AJAX serta integrasi Native WordPress Bulk & Quick Edit.
 * Version:     2.0.0
 * Author:      M. Alfiandi Ismet
 * Author URI:  https://github.com/m-alfiandiismet
 * License:     GPL-2.0+
 * Text Domain: tokoku-bulk-editor
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Cegah redeclare fatal error apabila modul sudah dimuat dari tema atau plugin mandiri
if ( defined( 'TOKOKU_BULK_EDITOR_VERSION' ) || class_exists( 'Tokoku_Bulk_Editor_Plugin' ) || function_exists( 'tokoku_bulk_editor_init' ) ) {
    return;
}

// Define Constants
if ( ! defined( 'TOKOKU_BULK_EDITOR_VERSION' ) ) {
    define( 'TOKOKU_BULK_EDITOR_VERSION', '2.0.0' );
}
if ( ! defined( 'TOKOKU_BULK_EDITOR_FILE' ) ) {
    define( 'TOKOKU_BULK_EDITOR_FILE', __FILE__ );
}
if ( ! defined( 'TOKOKU_BULK_EDITOR_DIR' ) ) {
    define( 'TOKOKU_BULK_EDITOR_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'TOKOKU_BULK_EDITOR_URL' ) ) {
    // Support execution both as a standalone plugin in wp-content/plugins or bundled in theme
    if ( strpos( __FILE__, wp_normalize_path( WP_PLUGIN_DIR ) ) !== false || ( defined( 'WPMU_PLUGIN_DIR' ) && strpos( __FILE__, wp_normalize_path( WPMU_PLUGIN_DIR ) ) !== false ) ) {
        define( 'TOKOKU_BULK_EDITOR_URL', plugin_dir_url( __FILE__ ) );
    } else {
        define( 'TOKOKU_BULK_EDITOR_URL', get_template_directory_uri() . '/tokoku-bulk-editor/' );
    }
}

// Load Core Classes
require_once TOKOKU_BULK_EDITOR_DIR . 'includes/class-product-manager.php';
require_once TOKOKU_BULK_EDITOR_DIR . 'includes/class-stock-manager.php';
require_once TOKOKU_BULK_EDITOR_DIR . 'includes/class-native-bulk-edit.php';
require_once TOKOKU_BULK_EDITOR_DIR . 'includes/class-admin-page.php';

if ( ! class_exists( 'Tokoku_Bulk_Editor_Plugin' ) ) {
    /**
     * Main TokoKu Bulk Editor Plugin Bootstrap
     */
    class Tokoku_Bulk_Editor_Plugin {

        /**
         * Singleton instance
         */
        private static $instance = null;

        /**
         * Product Manager instance
         */
        public $product_manager;

        /**
         * Backward-compatible Stock Manager alias
         */
        public $stock_manager;

        /**
         * Native Bulk Edit instance
         */
        public $native_edit;

        /**
         * Admin Dashboard instance
         */
        public $admin_page;

        /**
         * Get instance
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         */
        private function __construct() {
            add_action( 'init', array( $this, 'load_textdomain' ) );
            $this->init_components();
        }

        /**
         * Load Plugin Textdomain
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'tokoku-bulk-editor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        /**
         * Initialize Components
         */
        private function init_components() {
            $this->product_manager = Tokoku_Product_Manager::get_instance();
            $this->stock_manager   = $this->product_manager;
            $this->native_edit     = Tokoku_Native_Bulk_Edit::get_instance();
            $this->admin_page      = Tokoku_Bulk_Editor_Admin_Page::get_instance();
        }
    }
}

if ( ! function_exists( 'tokoku_bulk_editor_init' ) ) {
    /**
     * Initialize Plugin
     */
    function tokoku_bulk_editor_init() {
        return Tokoku_Bulk_Editor_Plugin::get_instance();
    }
}

// Boot plugin safely
if ( did_action( 'plugins_loaded' ) ) {
    tokoku_bulk_editor_init();
} else {
    add_action( 'plugins_loaded', 'tokoku_bulk_editor_init' );
}
