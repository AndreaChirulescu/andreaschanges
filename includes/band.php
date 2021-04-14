<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Band') ) {
    class GiglogAdmin_Band
    {
        static function create($name, $country)
        {
            global $wpdb;
            if (empty($country)) $country = 'NO';
            $wpdb->insert('wpg_bands', array(
                'id' => '',
                'wpgband_name' => $name,
                'wpgband_country' => $country
            ));

            return $wpdb->insert_id;
        }

        static function find_or_create($name, $country)
        {
            global $wpdb;
            if(empty($country)) $country = 'NO';
            $bandsql = 'SELECT id FROM wpg_bands WHERE upper(wpgband_name)="' . $name . '"';
            $results = $wpdb->get_results($bandsql);

            return $results ? $results[0]->id : GiglogAdmin_Band::create($name, $country);
        }

        static function all_bands()
        {
            global $wpdb;

            $results = $wpdb->get_results("select id, wpgband_name as vname from wpg_bands order by wpgband_name");

            return ($results);
        }

        static function all_countries()
        {
            global $wpdb;

            $results = $wpdb->get_results("select id, wpgcountry_name as cname from wpg_countries order by id");

            return ($results);
        }

    }
}
