<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_Concertlogs' ) )
{
    class GiglogAdmin_Concertlogs
    {
        /**
         * Adds a default entry for the given concert id in the
         * concert logs table.
         */
        public static function add($concert_id)
        {
            global $wpdb;

            $q = $wpdb->prepare(
                "INSERT INTO wpg_concertlogs SET wpgcl_concertid = %d",
                intval($concert_id));

            $wpdb->query($q);
        }
    }
}
