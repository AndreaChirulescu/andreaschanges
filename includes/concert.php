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

if ( !class_exists('GiglogAdmin_Concert') ) {
    class GiglogAdmin_Concert
    {
        private $id;
        private $band;
        private $venue;
        private $cdate;
        private $tickets;
        private $eventlink;

         /*
         * Constructs a new venue object from an array of attributes.
         * The attributes are expected to be named as in the database,
         * so this constructor can be used to construct the object
         * directly from the database row.
         */
        private function __construct($attrs)
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;
            $this->band = isset($attrs->band) ? $attrs->band : NULL;
            $this->venue = isset($attrs->venue) ? $attrs->venue : NULL;
            $this->cdate = isset($attrs->wpgconcert_date) ? $attrs->wpgconcert_date : NULL;
            $this->tickets = isset($attrs->wpgconcert_tickets) ? $attrs->wpgconcert_tickets : NULL;
            $this->eventlink = isset($attrs->wpgconcert_event) ? $attrs->wpgconcert_event : NULL;
        }

        static function find_or_create($id,$band, $venue, $cdate, $ticketlink, $eventlink)
        {
            global $wpdb;
            if($id)
            {
                $csql = 'SELECT * FROM wpg_concerts WHERE id="' . $id . '"';
                $results  = $wpdb->get_results($csql);

                if ($results)
                    return new GiglogAdmin_Concert($results[0]);
            }
            else {

                return GiglogAdmin_Concert::create($band, $venue, $cdate, $ticketlink, $eventlink);
            }
        }

        public static function create($band, $venue, $cdate, $ticketlink, $eventlink)
        {
            $attrs = new stdClass();
            $attrs->id = '';
            $attrs->band = $band;
            $attrs->venue = $venue;
            $attrs->wpgconcert_date = $cdate;
            $attrs->wpgconcert_tickets = $ticketlink;
            $attrs->wpgconcert_event = $eventlink;
            $cid = new GiglogAdmin_Concert($attrs);
            $cid->save();

            return $cid;
        }


        public static function updatec($id, $band, $venue, $cdate, $ticketlink, $eventlink)
        {
            global $wpdb;

            $res = $wpdb->update('wpg_concerts', array(
                'band' => $band,
                'venue' => $venue,
                'wpgconcert_date' => $cdate,
                'wpgconcert_tickets' => $ticketlink,
                'wpgconcert_event' => $eventlink
            ),
                array('id' => $id)
            );

            if ( !$res ) {
             //   exit( var_dump( $wpdb->last_query ) ); //for onscreen debugging when needed
                error_log( __CLASS__ . '::' . __FUNCTION__ . ": {$wpdb->last_error}");
                die;
            }

            return ($wpdb->last_error); //not sure what to return here?
        }

        public static function get($band, $venue, $date)
        {
            global $wpdb;

            $sql = 'SELECT id from wpg_concerts'
                . ' where band = ' . $band
                . ' and venue = ' . $venue
                . ' and wpgconcert_date ="' . $date . '"';

            error_log(__CLASS__ . '::' . __FUNCTION__ . ": {$sql}");
            return $wpdb->get_results($sql);
        }

        public function save()
        {
            global $wpdb;

            $wpdb->insert('wpg_concerts', array(
                'id' => '',
                'band' => $this->band,
                'venue' => $this->venue,
                'wpgconcert_date' => $this->cdate,
                'wpgconcert_tickets' => $this->tickets,
                'wpgconcert_event' => $this->eventlink
            ));

            $this->id = $wpdb->insert_id;
        }

        public function id()
        {
            return $this->id;
        }

        public function band()
        {
            return $this->band;
        }
        public function venue()
        {
            return $this->venue;
        }
        public function cdate()
        {
            return $this->cdate;
        }
        public function tickets()
        {
            return $this->tickets;
        }
        public function eventlink()
        {
            return $this->eventlink;
        }
    }
}
?>
