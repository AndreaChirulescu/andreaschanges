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

if ( !class_exists('GiglogAdmin_Band') ) {
    class GiglogAdmin_Band
    {
        static function create($name)
        {
            global $wpdb;

            $wpdb->insert('wpg_bands', array(
                'id' => '',
                'wpgband_name' => $name
            ));

            return $wpdb->insert_id;
        }

        static function find_or_create($name)
        {
            global $wpdb;

            $bandsql = 'SELECT id FROM wpg_bands WHERE upper(wpgband_name)="' . $name . '"';
            $results = $wpdb->get_results($bandsql);

            return $results ? $results[0]->id : GiglogAdmin_Band::create($name);
        }

        static function all_bands()
        {
            global $wpdb;

            $results = $wpdb->get_results("select id, wpgband_name as vname from wpg_bands order by wpgband_name");

            return ($results);
        }

    }
}
