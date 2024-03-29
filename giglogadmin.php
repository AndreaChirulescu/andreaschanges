<?php
/**
 * Gigolog Admin
 *
 * @package     giglogadmin
 * @author      Andrea Chirulescu, Harald Eilertsen
 * @copyright   2021 Andrea Chirulescu, Harald Eilertsen
 * @license     AGPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Giglog Admin
 * Plugin URI:  https://code.volse.no/wp/plugins/giglogadmin
 * Description: Scheduling journalists and photographers to cover concerts or events.
 * Version:     0.1.0
 * Author:      Andrea Chirulescu, Harald Eilertsen
 * License:     AGPLv3
 * License URI: https://www.gnu.org/licenses/agpl-3.0.txt
 *
 * SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
 * SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

if ( !class_exists( 'GiglogAdmin_Plugin' ) ) {

    class GiglogAdmin_Plugin
    {
        static public function init(): void {
            if ( !defined('GIGLOGADMIN_UNIT_TEST') ) {
                require_once __DIR__ . '/includes/admin/register_db_tables.php';
            }

            require_once __DIR__ . '/vendor/autoload.php';
            require_once __DIR__ . '/includes/venue.php';
            require_once __DIR__ . '/includes/concert.php';
            require_once __DIR__ . '/includes/view-helpers/select_field.php';
            require_once __DIR__ . '/includes/ical_export.php';

            if (is_admin()) {
                require_once __DIR__ . '/includes/admin/views/giglog_admin_page.php';
                require_once __DIR__ . '/includes/admin/views/giglog_import_gigs.php';
                require_once __DIR__ . '/includes/admin/helpfiles/instrunctions.php';
                require_once __DIR__ . '/includes/admin/helpfiles/instr_reviewers.php';
                require_once __DIR__ . '/includes/admin/helpfiles/instr_photog.php';

                add_action( 'admin_menu', array( 'GiglogAdmin_Plugin', 'add_admin_pages' ));
                add_action( 'admin_menu', array( 'GiglogAdmin_Plugin', 'add_help_pages' ));

                add_filter( 'wp_nav_menu_args', array( 'GiglogAdmin_Plugin', 'nav_menu_args' ));
            }
            else {
                require_once __DIR__ . '/includes/admin/views/_concerts_table.php';
                require_once __DIR__ . '/includes/giglog_visitor_display.php';
            }
        }

        static function activate(): void {
        }

        static function deactivate(): void {
        }

        /**
         * Adds the 'Giglog' top level menu to the left side WordPress admin
         * menu. Other subpages will come later.
         *
         * @return void
         */
        static function add_admin_pages(): void {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            $top = add_menu_page(
                "Giglog admin",             // Page title
                "Giglog",                   // Menu title
                "upload_files",             // Will show for users with this capability
                "giglog",                   // menu slug
                array( 'GiglogAdmin_AdminPage', 'render_html' ),     // callable
                'dashicons-tickets-alt',    // Icon url
                11);                        // Position, just below 'Media'

            add_action( 'load-' . $top, array( 'GiglogAdmin_AdminPage', 'update' ) );

            $import_hook = add_submenu_page(
                "giglog",                   // parent slug
                "Import gigs",              // page title
                "Import gigs",              // menu title
                "upload_files",             // required capability
                "giglog_import",            // menu slug
                array( 'GiglogAdmin_ImportGigsPage', 'render_html' ));   // callable

            if ($import_hook !== false) {
                add_action(
                    'load-' . $import_hook,
                    array( 'GiglogAdmin_ImportGigsPage', 'submit_form' ) );
            }

            wp_register_style( 'css_style', plugins_url( '/includes/css/main.css', __FILE__ ) );
            wp_enqueue_style('css_style');
        }

        static function add_help_pages(): void {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            add_menu_page(
                "Help for ET users",        // Page title
                "Help for ET users",        // Menu title
                "upload_files",             // Will show for users with this capability
                "helpfiles",                // menu slug
                array( 'Instructions_Page', 'render_instr_html' ),     // callable
                'dashicons-tickets-alt',    // Icon url
                10);                        // Position, just below 'Media'

            add_submenu_page(
                "helpfiles",                // parent slug
                "Reviewer help files",      // page title
                "Reviewer help files",      // menu title
                "upload_files",             // required capability
                "reviewer_help",            // menu slug
                array( 'Instructions_Reviewers', 'render_instr_rev_html' ));   // callable

            add_submenu_page(
                "helpfiles",                 // parent slug
                "Photogalleries help files", // page title
                "Photogalleries help files", // menu title
                "upload_files",              // required capability
                "photog_help",               // menu slug
                array( 'Instructions_Photogs', 'render_instr_photo_html' ));   // callable
        }

        /*
         * Show menus based on whether user is logged in or not.
         *
         * Giglog admin pages should only be visible for logged in users/can eventually
         * be customized by role if needed
         */
        static function nav_menu_args( array $args = [] ) : array {
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
