<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Concert') ) {
    class GiglogAdmin_Concert
    {
        private $id;
        private $cname;
        private $venue;
        private $cdate;
        private $tickets;
        private $eventlink;

        /*
         * Constructs a new concert object from an array of attributes.
         * The attributes are expected to be named as in the database,
         * so this constructor can be used to construct the object
         * directly from the database row.
         */
        public function __construct($attrs = [])
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;
            $this->cname = isset($attrs->wpgconcert_name) ? $attrs->wpgconcert_name : NULL;
            $this->venue = isset($attrs->venue) ? $attrs->venue : NULL;
            $this->cdate = isset($attrs->wpgconcert_date) ? $attrs->wpgconcert_date : NULL;
            $this->tickets = isset($attrs->wpgconcert_tickets) ? $attrs->wpgconcert_tickets : NULL;
            $this->eventlink = isset($attrs->wpgconcert_event) ? $attrs->wpgconcert_event : NULL;
        }


        static function find_cid($id)
        {
            global $wpdb;

            if(!empty($id))
            {
                $csql = 'SELECT * FROM wpg_concerts WHERE id="' . $id . '"';
                $results  = $wpdb->get_results($csql);
                if ($results)
                {
                    return new GiglogAdmin_Concert($results[0]);
                }
            }
            else
            {
                return new GiglogAdmin_Concert();
            }
        }

        static function check_duplicate($cname, $venue, $cdate, $ticketlink, $eventlink)
        {
            global $wpdb;

            $cresults = GiglogAdmin_Concert::get($cname, $venue, $cdate);
            if ($cresults)
                return($cresults);
            else
                return ('new');

        }
        public static function create($cname, $venue, $cdate, $ticketlink, $eventlink)
        {   $c = GiglogAdmin_Concert::check_duplicate($cname, $venue, $cdate, $ticketlink, $eventlink);
            if ($c=='new')
            {
                $attrs = new stdClass();
                $attrs->id = '';
                $attrs->wpgconcert_name = $cname;
                $attrs->venue = $venue;
                $attrs->wpgconcert_date = $cdate;
                $attrs->wpgconcert_tickets = $ticketlink;
                $attrs->wpgconcert_event = $eventlink;
                $cid = new GiglogAdmin_Concert($attrs);
                $cid->save();

                error_log( 'NEW CONCERT ADDED: '
                . ' ID: ' . $cid -> id()
                . ' CONCERT NAME ' . $cname
                . ', VENUE ID ' . $venue
                . ', CONCERTDATE ' . $cdate
                . ', Ticket LINK ' . $ticketlink
                . ', Event LINK ' . $eventlink);
                GiglogAdmin_Concertlogs::add($cid->id());
                    /*the last line can be replaced by a trigger
                    CREATE TRIGGER `insertIntoPhotoLogs` AFTER INSERT ON `wpg_concerts`
                    FOR EACH ROW INSERT INTO wpg_concertlogs (
                    wpg_concertlogs.id,
                    wpg_concertlogs.wpgcl_concertid,
                    wpg_concertlogs.wpgcl_status)

                    VALUES
                    (null, new.id, 1)
                    */
                return $cid;
            }
            else
            {
                error_log( 'DUPLICATE ROW detected: '
                . ' CONCERT NAME ' . $cname
                . ', VENUE  ID ' . $venue
                . ', CONCERTDATE ' . $cdate);
                return('dup');
            }
        }


        public static function update_concert($id, $cname, $venue, $cdate, $ticketlink, $eventlink)
        {
            global $wpdb;

            $res = $wpdb->update('wpg_concerts', array(
                'wpgconcert_name' => $cname,
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

            return ($wpdb->last_error);
        }

        static function update_concertlog($cid, $ph1, $ph2, $rev1, $rev2)
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

        public static function get($cname, $venue, $date)
        {
            global $wpdb;

            $sql = 'SELECT id from wpg_concerts'
                . ' where wpgconcert_name = "' . $cname .'"'
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
                'wpgconcert_name' => $this->cname,
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

        public function cname()
        {
            return $this->cname;
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
