<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Venue') ) {
    class GiglogAdmin_Venue
    {
        private $id;
        private $name;
        private $city;
        private $address;
        private $webpage;

        /*
         * Constructs a new venue object from an array of attributes.
         * The attributes are expected to be named as in the database,
         * so this constructor can be used to construct the object
         * directly from the database row.
         */
        private function __construct($attrs)
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;
            $this->name = isset($attrs->wpgvenue_name) ? $attrs->wpgvenue_name : NULL;
            $this->city = isset($attrs->wpgvenue_city) ? $attrs->wpgvenue_city : NULL;
            $this->address = isset($attrs->wpgvenue_address) ? $attrs->wpgvenue_address : NULL;
            $this->webpage = isset($attrs->wpgvenue_webpage) ? $attrs->wpgvenue_webpage : NULL;
        }

        static function create($name,$city)
        {
            $venue = new GiglogAdmin_Venue((object) [
                'wpgvenue_name' => $name,
                'wpgvenue_city' => !empty($city) ? $city : 'Oslo',
            ]);
            $venue->save();

            return $venue;
        }

        static function find_or_create($name, $city)
        {
            global $wpdb;
            if(empty($city)) $city='Oslo';
            $venuesql = 'SELECT * FROM wpg_venues WHERE upper(wpgvenue_name)="' . $name . '"';
            $results  = $wpdb->get_results($venuesql);

            if ($results) {
                return new GiglogAdmin_Venue($results[0]);
            }
            else {
                return GiglogAdmin_Venue::create($name, $city);
            }
        }

        static function all_cities()
        {
            global $wpdb;
            $results = $wpdb->get_results('select distinct wpgvenue_city from wpg_venues');

            return array_map(function ($r) { return $r->wpgvenue_city; }, $results);
        }

        static function all_venues()
        {
            global $wpdb;

            $results = $wpdb->get_results("select * from wpg_venues");

            return array_map(function ($r) { return new GiglogAdmin_Venue($r); }, $results);
        }


        static function venues_in_city($city)
        {
            global $wpdb;
            $q = $wpdb->prepare(
                "select id, wpgvenue_name from wpg_venues where wpgvenue_city=?", $city);
            $results = $wpdb->get_results($q);

            return array_map(function ($r) { return new GiglogAdmin_Venue($r); }, $results);
        }

        public function save()
        {
            global $wpdb;

            $wpdb->insert('wpg_venues', array(
                'id' => '',
                'wpgvenue_name' => $this->name
            ));

            $this->id = $wpdb->insert_id;
        }

        public function id()
        {
            return $this->id;
        }

        public function name()
        {
            return $this->name;
        }

        public function city()
        {
            return $this->city;
        }
    }
}
