<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !function_exists( "giglog_register_db_tables") )
{
    /**
     * Registers the tables used by the GiglogAdmin plugin
     */
    function giglog_register_db_tables() : void
    {
        global $wpdb;

        $tables = [];
        $tables[] =
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}giglogadmin_venues` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgvenue_name` VARCHAR(500) NOT NULL,
                `wpgvenue_city` VARCHAR(250) DEFAULT NULL,
                `wpgvenue_address` VARCHAR(2000) DEFAULT NULL,
                `wpgvenue_webpage` VARCHAR(200) DEFAULT NULL,
                `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $tables[] =
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}giglogadmin_concerts` (
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
                CONSTRAINT `wpgconcert_venue`
                    FOREIGN KEY (`venue`)
                    REFERENCES `{$wpdb->prefix}giglogadmin_venues` (`id`) ON DELETE NO ACTION
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


        foreach($tables as $tabledef) {
            $result = $wpdb->query($tabledef);
            if ($result === false) {
                error_log("Registering table failed.");
            }
        }
    }

    giglog_register_db_tables();
}
