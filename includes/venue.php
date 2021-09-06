<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists('GiglogAdmin_Venue') ) {
    class GiglogAdmin_Venue
    {
        private ?int $id;

        /**
         * @psalm-suppress PropertyNotSetInConstructor
         */
        private string $name;

        /**
         * @psalm-suppress PropertyNotSetInConstructor
         */
        private string $city;
        private ?string $address;
        private ?string $webpage;

        /*
         * Constructs a new venue object from an array of attributes.
         * The attributes are expected to be named as in the database,
         * so this constructor can be used to construct the object
         * directly from the database row.
         */
        public function __construct(object $attrs)
        {
            $this->id = isset($attrs->id) ? $attrs->id : NULL;

            if (isset($attrs->wpgvenue_name, $attrs->wpgvenue_city)) {
                $this->name = $attrs->wpgvenue_name;
                $this->city = $attrs->wpgvenue_city;
            }
            else {
                error_log('Trying to construct a Venue without a name or a city');
                wp_die();
            }

            $this->address = isset($attrs->wpgvenue_address) ? $attrs->wpgvenue_address : NULL;
            $this->webpage = isset($attrs->wpgvenue_webpage) ? $attrs->wpgvenue_webpage : NULL;
        }

        /**
         * Get venue by given id.
         *
         * @param int $id
         * @return null|self
         */
        static function get(int $id) : ?self
        {
            global $wpdb;

            $query = $wpdb->prepare('SELECT * from wpg_venues WHERE id = %d', $id);
            $results = $wpdb->get_results($query);

            return $results ? new GiglogAdmin_Venue($results[0]) : NULL;
        }

        static function create($name, $city = 'Oslo'): self
        {
            $venue = new GiglogAdmin_Venue((object) [
                'wpgvenue_name' => $name,
                'wpgvenue_city' => $city,
            ]);
            $venue->save();

            return $venue;
        }

        static function find_or_create(string $name, string $city = 'Oslo'): self
        {
            global $wpdb;
            $venuesql = 'SELECT * FROM wpg_venues WHERE upper(wpgvenue_name)=upper("' . $name . '")'.' and wpgvenue_city="'.$city.'"';
            $results  = $wpdb->get_results($venuesql);

            if ($results) {
                return new GiglogAdmin_Venue($results[0]);
            }
            else {
                return GiglogAdmin_Venue::create($name, $city);
            }
        }

        static function all_cities(): array
        {
            global $wpdb;
            $results = $wpdb->get_results('select distinct wpgvenue_city from wpg_venues');

            return array_map(function ($r) { return $r->wpgvenue_city; }, $results);
        }

        /**
         * @return self[]
         *
         * @psalm-return array<array-key, self>
         */
        static function all_venues(): array
        {
            global $wpdb;

            $results = $wpdb->get_results("select * from wpg_venues ORDER BY wpgvenue_name");

            return array_map(function ($r) { return new GiglogAdmin_Venue($r); }, $results);
        }


        /**
         * @return self[]
         *
         * @psalm-return array<array-key, self>
         */
        static function venues_in_city(string $city): array
        {
            global $wpdb;
            $q = $wpdb->prepare("select * from wpg_venues where wpgvenue_city=%s", $city);
            $q .=" ORDER BY wpgvenue_name";
            $results = $wpdb->get_results($q);

            return array_map(function ($r) { return new GiglogAdmin_Venue($r); }, $results);
        }

        public function save(): void
        {
            global $wpdb;

            $wpdb->insert('wpg_venues', array(
                'id' => '',
                'wpgvenue_name' => $this->name,
                'wpgvenue_city' => $this->city,
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
