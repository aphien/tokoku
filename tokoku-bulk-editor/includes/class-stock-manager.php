<?php
/**
 * Stock Manager Core Logic (Compatibility Layer)
 * Extends Tokoku_Product_Manager to maintain full backward compatibility
 *
 * @package TokoKu_Bulk_Editor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once dirname( __FILE__ ) . '/class-product-manager.php';

class Tokoku_Stock_Manager extends Tokoku_Product_Manager {

    /**
     * Singleton instance
     */
    protected static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
