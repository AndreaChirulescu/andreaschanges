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
        static function create($name)
        {
            global $wpdb;

            $wpdb->insert('wpg_venues', array(
                'id' => '',
                'wpgvenue_name' => $name
            ));

            return $wpdb->insert_id;
        }

        static function find_or_create($name)
        {
            global $wpdb;

            $venuesql = 'SELECT id FROM wpg_venues WHERE upper(wpgvenue_name)="' . $name . '"';
            $results  = $wpdb->get_results($venuesql);

            return $results ? $results[0]->id : GiglogAdmin_Venue::create($name);
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
            
            $results = $wpdb->get_results("select id, CONCAT( IFNULL(wpgvenue_name,''),'-',IFNULL(wpgvenue_city,'')) as vname from wpg_venues");

            return ($results);
        }


        static function venues_in_city($city)
        {
            global $wpdb;
            $q = $wpdb->prepare(
                "select id, wpgvenue_name from wpg_venues where wpgvenue_city=?", $city);
            $results = $wpdb->get_results($q);

            return array_map(function ($r) { return [$r->id, $r->wpgvenue_name]; }, $results);
        }
    }
}
