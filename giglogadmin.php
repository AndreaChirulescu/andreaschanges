<?php
/**
 * Gigolog Admin
 *
 * @package     giglogadmin
 * @author      Andrea Chirulescu, Harald Eilertsen
 * @copright    2021 Andrea Chirulescu, Harald Eilertsen
 * @license     AGPL-3.0
 *
 * @wordpress-plugin
 * Plugin Name: Giglog Admin
 * Plugin URI:  https://code.volse.no/wp/plugins/giglogadmin
 * Description: Scheduling journalists and photographers to cover concerts or events.
 * Version:     0.1.0
 * Author:      Andrea Chirulescu, Harald Eilertsen
 * License:     AGPLv3
 * License URI: https://www.gnu.org/licenses/agpl-3.0.txthttps://www.gnu.org/licenses/agpl-3.0.txt
 */



if ( !class_exists( 'GiglogAdmin_Plugin' ) ) {
    require_once __DIR__ . '/includes/public/shortcodes/giglog_bands.php';
    require_once __DIR__ . '/includes/public/shortcodes/giglog_display_unprocessed.php';
    require_once __DIR__ . '/includes/public/shortcodes/giglog_photographers.php';
    require_once __DIR__ . '/includes/public/shortcodes/giglog_process_files.php';

    class GiglogAdmin_Plugin
    {
        static public function init() {
            add_shortcode('giglog_cities', 'giglogadmin_getfilters');
            add_shortcode('giglog_bands', 'giglogadmin_getconcerts');
            add_shortcode('giglog_unprocessed', 'giglogadmin_display_unprocessed');
            add_shortcode('giglog_upload', 'giglogadmin_upload_files');
            add_shortcode('giglog_photog', 'giglogadmin_photographers');

            add_filter( 'wp_nav_menu_args', array( 'GiglogAdmin_Plugin', 'nav_menu_args' ));
        }

        static function activate() {
            require_once __DIR__ . '/includes/admin/register_db_tables.php';
        }

        static function deactivate() {
        }

        /*
         * Show menus based on whether user is logged in or not.
         *
         * Giglog admin pages should only be visible for logged in users/can eventually
         * be customized by role if needed
         */
        static function nav_menu_args( $args = '' ) {
            if ( is_user_logged_in() ) {
                $args['menu'] = 'Loggedusers';
            } else {
                $args['menu'] = 'Notloggedusers';
            }

            return $args;
        }

    }

    register_activation_hook( __FILE__, array( 'GiglogAdmin_Plugin', 'activate' ));
    register_deactivation_hook( __FILE__, array( 'GiglogAdmin_Plugin', 'deactivate' ));

    GiglogAdmin_Plugin::init();
}
?>
