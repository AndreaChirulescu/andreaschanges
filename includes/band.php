<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Band') ) {
    class GiglogAdmin_Band
    {
        private $id;
        private $bandname;
        private $country;


        public function __construct($attrs = [])
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;
            $this->bandname = isset($attrs->bandname) ? $attrs->bandname : NULL;
            $this->country = isset($attrs->country) ? $attrs->country : 'NO';
        }

        static function create($bandname, $country = 'NO')
        {
            global $wpdb;

            $bandsql = 'SELECT id FROM wpg_bands WHERE upper(wpgband_name)="' . $bandname . '" and wpgband_country = "'.$country.'"';
            $results = $wpdb->get_results($bandsql);
            $attrs = new stdClass();
            $attrs->bandname = $bandname;
            $attrs->country = $country;

            if ($results)
            {
                $attrs->id = $results[0]->id;
                $bid = new GiglogAdmin_Band($attrs);
            }
            else
            {
                $attrs->id = '';

                $bid = new GiglogAdmin_Band($attrs);
                $bid->save();


                error_log( 'NEW BAND ADDED: '
                    . ' ID: ' . $bid -> id()
                    . ' BAND NAME ' . $bandname
                    . ', COUNTRY ' . $country);
            }

            return $bid;
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

        static function get_band($bid)
        {
            global $wpdb;
            if(!empty($bid))
            {

            $results = $wpdb->get_results('select wpgband_name as bname, wpgband_country as cname from wpg_bands where wpg_bands.id = '.$bid );

            return array ($results[0]->bname,$results[0]->cname);
            }
            else return('');
        }

        static function update_band($bid,$bname,$bcountry)
        {
            global $wpdb;

            $res = $wpdb->update('wpg_bands', array(
                'wpgband_name' => $bname,
                'wpgband_country' => $bcountry
            ),
                array('id' => $bid)
            );

            if ( !$res ) {
             //   exit( var_dump( $wpdb->last_query ) ); //for onscreen debugging when needed
                error_log( __CLASS__ . '::' . __FUNCTION__ . ": {$wpdb->last_error}");
                die;
            }

            return ($wpdb->last_error);
        }

        public function save()
        {
            global $wpdb;

            $wpdb->insert('wpg_bands', array(
                'id' => '',
                'wpgband_name' => $this->bandname,
                'wpgband_country' => $this->country
            ));

            $this->id = $wpdb->insert_id;
        }

        public function id()
        {
            return $this->id;
        }

        public function bandname()
        {
            return $this->bandname;
        }
        public function country()
        {
            return $this->country;
        }
    }
}
