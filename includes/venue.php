<?php
/*
 * Copyright (C) 2021 Harald Eilertsen, Andrea Chirulescu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
        {   if(empty($city)) $city='Oslo';
            $attrs = new stdClass();
            $attrs->wpgvenue_name = $name;
            $attrs->wpgvenue_city = $city;
            $venue = new GiglogAdmin_Venue($attrs);
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
