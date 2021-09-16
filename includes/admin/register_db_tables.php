<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if (!function_exists('giglogadmin_populate_countries')) {
    function giglogadmin_populate_countries(): void
    {
        global $wpdb;
        //not removing yet as I haven't yet checked where else it might be called
    }
}

if ( !function_exists( "giglog_register_db_tables") )
{
    /**
     * @return void
     */
    function giglog_register_db_tables()
    {
        global $wpdb;

        // Clean out obsolete tables if they exist.
        $wpdb->query("DROP TABLE IF EXISTS "
            . "wpg_bands, wpg_concertlogs, wpg_files, wpg_logchanges, wpg_pressstatus");
        /* not sure if DB version needed for now, leaving code here in case we decide to use it. To be removed if not
        $db_version = get_option('giglogadmin_db_version');
        if ($db_version == 8) {
            return;
        }
        */

        $concerts_table =
            "CREATE TABLE IF NOT EXISTS `wpg_concerts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgconcert_name` VARCHAR(2000) NOT NULL,
                `venue` int(11) NOT NULL,
                `wpgconcert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `wpgconcert_tickets` VARCHAR(2000) DEFAULT NULL,
                `wpgconcert_event` VARCHAR(2000) DEFAULT NULL,
                `wpgconcert_type` INT NOT NULL DEFAULT '1' COMMENT '1 concert, 2 festival',
                `wpgconcert_status` INT DEFAULT 1,
                `wpgconcert_roles` JSON CHECK (JSON_VALID(wpgconcert_roles)),
                `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `wpgconcert_venue` (`venue`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


        $venues_table =
            "CREATE TABLE IF NOT EXISTS `wpg_venues` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgvenue_name` VARCHAR(500) NOT NULL,
                `wpgvenue_city` VARCHAR(250) DEFAULT NULL,
                `wpgvenue_address` VARCHAR(2000) DEFAULT NULL,
                `wpgvenue_webpage` VARCHAR(200) DEFAULT NULL,
                `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $tables = array(
            $concerts_table,
            $venues_table);

        foreach($tables as $tabledef) {
            $result = $wpdb->query($tabledef);
            if ($result === false) {
                error_log("Registering table failed.");
            }
        }


            $wpdb->query(
                "ALTER TABLE `wpg_concerts`
                    ADD CONSTRAINT `wpgconcert_venue`
                        FOREIGN KEY (`venue`)
                        REFERENCES `wpg_venues` (`id`) ON DELETE NO ACTION;");

        // update_option("giglogadmin_db_version", 8);
    }

    giglog_register_db_tables();
}

?>
