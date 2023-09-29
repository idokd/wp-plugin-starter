<?php
/**
 * CONF Plugin Name.
 *
 * @package   Plugin_Name_REST
 * @author    yalla ya!
 * @license   GPL-2.0+
 * @link      http://yalla-ya.com
 * @copyright 2022 yalla ya!
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");

/*-----------------------------------------*/

/**
 * Plugin REST
 */
class Plugin_Name_REST
{
   
    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;
 
    protected static $prefix = 'plugin_prefix';

    protected static $plugin_slug = null;

    protected static $plugin_version = null;

    protected static $capability = 'plugin_prefix_rest_api';

    /**
     * Initialize the class
     *
     * @since     1.0.0
     */
    private function __construct()
    {
        /**
         * Call $plugin_slug from public plugin class.
         */
        $plugin               = Plugin_Name::get_instance();
        self::$plugin_slug    = $plugin->get_plugin_slug();
        self::$plugin_version = $plugin->get_plugin_version();

        add_action( 'rest_api_init', function () {
      
            register_rest_route( 'plugin_prefix/v1', '/ping', [ // 
                'methods' => 'GET',
                'callback' => [ __CLASS__, 'ping' ],
                'permission_callback' => [ __CLASS__, 'permissions_check' ]
            ] );
        } );
    }

    public static function ping( $request ) {
        $prefix = 'plugin_prefix';
        $params = $request->get_json_params(); 
        if ( !$params ) {
            $params = $request->get_query_params();
        }
        return( [ 'pong' => $params ] );
    }


    public static function permissions_check() {
        if ( ( self::$capability  && current_user_can( self::$capability ) ) || current_user_can( 'administrator' ) ) return( true );
        return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not view private data.', 'plugin-name' ), array( 'status' => 401 ) );
    }

    public static function allow_all() {
        return( true );
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
