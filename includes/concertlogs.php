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
         *
         * @return void
         */
        public static function add($concert_id): void
        {
            global $wpdb;

            $q = $wpdb->prepare(
                "INSERT INTO wpg_concertlogs SET wpgcl_concertid = %d",
                intval($concert_id));

            $wpdb->query($q);
        }

        public static function get_status(int $concert_id) : ?int
        {
            global $wpdb;

            $q = $wpdb->prepare(
                'select wpgcl_status from wpg_concertlogs where id = %d',
                $concert_id);
            $res = $wpdb->get_results($q);

            return $res ? $res[0]->wpgcl_status : null;
        }
    }
}
