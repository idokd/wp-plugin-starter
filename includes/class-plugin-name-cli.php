<?php
/**
 * CONF Plugin Name.
 *
 * @package   Plugin_Name_CLI
 * @author    CONF_Plugin_Author
 * @license   GPL-2.0+
 * @link      CONF_Author_Link
 * @copyright CONF_Plugin_Copyright
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */

class Plugin_Name_Command {

    protected static $instance = null;

    public function __construct() {
        $plugin               = AOCR::get_instance();
        self::$plugin_slug    = $plugin->get_plugin_slug();
        self::$plugin_version = $plugin->get_plugin_version();
    }

 
    public function __invoke( $args ) {
        WP_CLI::success( implode( ',' , $args ) );
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

if ( class_exists( 'WP_CLI' ) ) WP_CLI::add_command( 'plugin_prefix', Plugin_Name_Command::get_instance() );