<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_Concertlogs' ) )
{
    class GiglogAdmin_Concertlogs
    {
        private array $roles;

        private function __construct( $attr = [] )
        {
            $this->roles['photo1'] = $attr->{"wpgcl_photo1"};
            $this->roles['photo2'] = $attr->{"wpgcl_photo2"};
            $this->roles['rev1'] = $attr->{"wpgcl_rev1"};
            $this->roles['rev2'] = $attr->{"wpgcl_rev2"};
        }

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

        static function update($cid, $ph1, $ph2, $rev1, $rev2)
        {
            global $wpdb;

            $res = $wpdb->update('wpg_concertlogs', array(
                'wpgcl_photo1' => $ph1,
                'wpgcl_photo2' => $ph2,
                'wpgcl_rev1' => $rev1,
                'wpgcl_rev2' => $rev2
            ),
                array('wpgcl_concertid' => $cid)
            );

            if ( !$res ) {
             //   exit( var_dump( $wpdb->last_query ) ); //for onscreen debugging when needed
                error_log( __CLASS__ . '::' . __FUNCTION__ . ": {$wpdb->last_error}");
                die;
            }

            return ($wpdb->last_error);
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

        public static function get(int $concert_id) : ?self
        {
            global $wpdb;

            $q = $wpdb->prepare(
                "select * from wpg_concertlogs where id = %d",
                $concert_id);

            $res = $wpdb->get_row($q);

            return $res ? new self($res) : null;
        }

        public function get_assigned_role(string $username) : ?string
        {
            return array_search( $username, $this->roles ) || NULL;
        }

        public function assigned_user(string $role) : ?string
        {
            return $this->roles[$role];
        }
    }
}
