<?php
/**
 * Gigolog Admin
 *
 * @package     gigologadmin
 * @author      Andrea Chirulescu, Harald Eilertsen
 * @copright    2021 Andrea Chirulescu, Harald Eilertsen
 * @license     AGPL-3.0
 *
 * @wordpress-plugin
 * Plugin Name: Gigolog Admin
 * Plugin URI:  https://code.volse.no/wp/plugins/gigologadmin
 * Description: Scheduling journalists and photographers to cover concerts or events.
 * Version:     0.1.0
 * Author:      Andrea Chirulescu, Harald Eilertsen
 * License:     AGPLv3
 * License URI: https://www.gnu.org/licenses/agpl-3.0.txthttps://www.gnu.org/licenses/agpl-3.0.txt
 */

if ( !class_exists( 'GigologAdmin_Plugin' ) ) {
    class GigologAdmin_Plugin
    {
        static function activate() {
            require_once __DIR__ . '/includes/admin/register_db_tables.php';
        }

        static function deactivate() {
        }
    }

    register_activation_hook( __FILE__, array( 'GigologAdmin_Plugin', 'activate' ));
    register_deactivation_hook( __FILE__, array( 'GigologAdmin_Plugin', 'deactivate' ));
}
?>
