<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

require_once __DIR__ . '/venue.php';

if ( !class_exists('GiglogAdmin_Concert') ) {
    require_once __DIR__ . '/venue.php';

    class GiglogAdmin_Concert
    {
        private ?int $id;
        private ?string $cname;
        private ?GiglogAdmin_Venue $venue;
        private ?string $cdate;
        private ?string $tickets;
        private ?string $eventlink;
        private ?int $status;
        private array $roles;

        public const STATUS_NONE = 0;
        public const STATUS_ACCRED_REQ = 1;
        public const STATUS_PHOTO_APPROVED = 2;
        public const STATUS_TEXT_APPROVED = 3;
        public const STATUS_ALL_APPROVED = 4;
        public const STATUS_REJECTED = 5;

        /*
         * Constructs a new concert object from an array of attributes.
         * The attributes are expected to be named as in the database,
         * so this constructor can be used to construct the object
         * directly from the database row.
         */
        public function __construct(object $attrs)
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;
            $this->cname = isset($attrs->wpgconcert_name) ? $attrs->wpgconcert_name : NULL;
            $this->cdate = isset($attrs->wpgconcert_date) ? $attrs->wpgconcert_date : NULL;
            $this->tickets = isset($attrs->wpgconcert_tickets) ? $attrs->wpgconcert_tickets : NULL;
            $this->eventlink = isset($attrs->wpgconcert_event) ? $attrs->wpgconcert_event : NULL;
            $this->status = isset($attrs->wpgconcert_status) ? $attrs->wpgconcert_status : 0;
            $this->roles = isset($attrs->wpgconcert_roles) ? json_decode($attrs->wpgconcert_roles, true) : [];

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
            else {
                $this->venue = NULL;
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

            if ( !$results ) {
                $wpdb->print_error( __METHOD__ );
                return null;
            }

            return new GiglogAdmin_Concert($results[0]);
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

                return $concert;
            }
        }


        public function update(object $attrs) : bool
        {
            $need_update = false;

            if (isset($attrs->wpgconcert_name) && $attrs->wpgconcert_name != $this->cname) {
                $this->cname = $attrs->wpgconcert_name;
                $need_update = true;
            }

            if (isset($attrs->wpgconcert_date) && $attrs->wpgconcert_date != $this->cdate) {
                $this->cdate = $attrs->wpgconcert_date;
                $need_update = true;
            }

            if (isset($attrs->wpgconcert_tickets) && $attrs->wpgconcert_tickets != $this->tickets) {
                $this->tickets = $attrs->wpgconcert_tickets;
                $need_update = true;
            }

            if (isset($attrs->wpgconcert_event) && $attrs->wpgconcert_event != $this->eventlink) {
                $this->eventling = $attrs->wpgconcert_eventlink;
                $need_update = true;
            }

            if (isset($attrs->wpgconcert_status) && $attrs->wpgconcert_status != $this->status) {
                $this->status = $attrs->wpgconcert_status;
                $need_update = true;
            }

            if (isset($attrs->wpgconcert_roles) && $attrs->wpgconcert_roles != $this->roles) {
                $this->roles = $attrs->wpgconcert_roles;
                $need_update = true;
            }

            if (isset($attrs->venue) && $attrs->venue != $this->venue()->id()) {
                $this->venue = GiglogAdmin_Venue::get($attrs->venue);
                $need_update = true;
            }

            if ($need_update) {
                $this->save();
            }

            return $need_update;
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

        public function save() : void
        {
            global $wpdb;

            $columns = [
                'wpgconcert_name' => $this->cname,
                'venue' => $this->venue->id(),
                'wpgconcert_date' => $this->cdate,
                'wpgconcert_tickets' => $this->tickets,
                'wpgconcert_event' => $this->eventlink,
                'wpgconcert_status' => $this->status,
                'wpgconcert_roles' => wp_json_encode( $this->roles ),
            ];

            if ( $this->id !== NULL ) {
                $res = $wpdb->update( 'wpg_concerts', $columns, [ 'id' => $this->id ] );
            }
            else {
                $res = $wpdb->insert('wpg_concerts', $columns);
            }

            if ( $res === false ) {
                $wpdb->print_error( __METHOD__ );
            }
            elseif ( $this->id === NULL ) {
                $this->id = $wpdb->insert_id;
            }
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

        public function status()
        {
            return $this->status;
        }

        public function set_status( int $new_status )
        {
            $this->status = $new_status;
        }

        /**
         * Return the roles defined for this concert.
         *
         * @return array<string, string>
         */
        public function roles() : array
        {
            return $this->roles;
        }

        public function assign_role( string $role, string $username ) : void
        {
            $this->roles[$role] = $username;
        }
    }
}
?>
