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

        public static function get_assigned_user( int $concert_id, string $role ) : ?string
        {
            global $wpdb;

            if ( ! in_array( $role, [ 'photo1', 'photo2', 'rev1', 'rev2' ] ) ) {
                error_log(__METHOD__ . ": invalid \$role ({$role}) given.");
                return null;
            }

            $column = "wpgcl_{$role}";
            $q = $wpdb->prepare(
                "select {$column} from wpg_concertlogs where id = %d",
                $concert_id);

            $res = $wpdb->get_row($q, ARRAY_A);

            return array_shift( $res );
        }
    }
}
