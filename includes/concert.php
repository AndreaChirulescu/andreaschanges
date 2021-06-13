<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Concert') ) {
    require_once __DIR__ . '/venue.php';

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
            $this->cdate = isset($attrs->wpgconcert_date) ? $attrs->wpgconcert_date : NULL;
            $this->tickets = isset($attrs->wpgconcert_tickets) ? $attrs->wpgconcert_tickets : NULL;
            $this->eventlink = isset($attrs->wpgconcert_event) ? $attrs->wpgconcert_event : NULL;


            if ( isset( $attrs->venue ) ) {
                if (isset($attrs->wpgvenue_name) && isset($attrs->wpgvenue_city)) {
                    $venue_attrs = (object) [
                        "id" => $attrs->venue,
                        "wpgvenue_name" => $attrs->wpgvenue_name,
                        "wpgvenue_city" => $attrs->wpgvenue_city,
                    ];

                    $this->venue = new GiglogAdmin_Venue($venue_attrs);
                }
                else {
                    $this->venue = GiglogAdmin_Venue::get($attrs->venue);
                }
            }
        }


        /**
         * Get concert with given id.
         *
         * @param int $id.
         * @return null|self.
         */
        static function get( int $id ) : ?self
        {
            global $wpdb;

            $query = 'SELECT wpg_concerts.*, wpg_venues.wpgvenue_name, wpg_venues.wpgvenue_city '
                . 'FROM wpg_concerts '
                . 'LEFT JOIN wpg_venues ON wpg_concerts.venue = wpg_venues.id '
                . 'WHERE ' . $wpdb->prepare('wpg_concerts.id = %d', $id);

            $results  = $wpdb->get_results($query);

            return $results ? new GiglogAdmin_Concert($results[0]) : NULL;
        }

        public static function create(string $name, $venue, string $date, string $ticketlink, string $eventlink): ?self
        {
            if ( GiglogAdmin_Concert::find($name, $venue, $date) ) {
                error_log( 'DUPLICATE ROW detected: '
                    . ' CONCERT NAME ' . $name
                    . ', VENUE  ID ' . $venue
                    . ', CONCERTDATE ' . $date);

                return NULL;
            }
            else {
                $concert = new GiglogAdmin_Concert( (object) [
                    "wpgconcert_name" => $name,
                    "venue" => $venue,
                    "wpgconcert_date" => $date,
                    "wpgconcert_tickets" => $ticketlink,
                    "wpgconcert_event" => $eventlink,
                ]);

                $concert->save();

                error_log( 'NEW CONCERT ADDED: '
                    . ' ID: ' . $concert -> id()
                    . ' CONCERT NAME ' . $name
                    . ', VENUE ID ' . $venue
                    . ', CONCERTDATE ' . $date
                    . ', Ticket LINK ' . $ticketlink
                    . ', Event LINK ' . $eventlink);

                GiglogAdmin_Concertlogs::add( $concert->id() );
                    /*the last line can be replaced by a trigger
                    CREATE TRIGGER `insertIntoPhotoLogs` AFTER INSERT ON `wpg_concerts`
                    FOR EACH ROW INSERT INTO wpg_concertlogs (
                    wpg_concertlogs.id,
                    wpg_concertlogs.wpgcl_concertid,
                    wpg_concertlogs.wpgcl_status)

                    VALUES
                    (null, new.id, 1)
                    */
                return $concert;
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

        public static function find($cname, $venue, $date)
        {
            global $wpdb;

            $sql = 'SELECT id from wpg_concerts'
                . ' where upper(wpgconcert_name) = upper("' . $cname .'")'
                . ' and venue = ' . $venue
                . ' and wpgconcert_date ="' . $date . '"';

            return $wpdb->get_results($sql);
        }


        /**
         * Return an array of concert objects optionally limited by a specified
         * filter.
         *
         * Valid filters are:
         *   - 'venue_id' => int : only include concerts at the given venue
         *   - 'city' => string  : only include concerts in the given city
         *
         * @param array<string, mixed> $filter
         * @return array<GiglogAdmin_Concert>
         */
        public static function find_concerts(array $filter = []) : array
        {
            global $wpdb;

            $query = 'SELECT wpg_concerts.*, wpg_venues.wpgvenue_name, wpg_venues.wpgvenue_city '
                . 'FROM wpg_concerts '
                . 'INNER JOIN wpg_venues ON wpg_concerts.venue = wpg_venues.id ';

            $where = [];

            if ( isset( $filter["city"] ) ) {
                array_push($where, 'wpg_venues.wpgvenue_city = ' . $wpdb->prepare('%s', $filter["city"]));
            }

            if ( isset( $filter["venue_id"] ) ) {
                array_push($where, 'wpg_venues.id = ' . $wpdb->prepare('%s', $filter["venue_id"]));
            }

            if ( ! empty( $where ) ) {
                $query .= 'WHERE ' . implode(' and ', $where);
            }

            $results  = $wpdb->get_results($query);

            return array_map(function($c) { return new GiglogAdmin_Concert($c); }, $results);
        }

        public function save(): void
        {
            global $wpdb;

            $wpdb->insert('wpg_concerts', array(
                'id' => '',
                'wpgconcert_name' => $this->cname,
                'venue' => $this->venue->id(),
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
