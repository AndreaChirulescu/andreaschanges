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
        public static function create($band, $venue, $date, $ticketlink, $eventlink)
        {
            global $wpdb;

            $wpdb->insert('wpg_concerts', array(
                'band' => $band,
                'venue' => $venue,
                'wpgconcert_date' => $date,
                'wpgconcert_tickets' => $ticketlink,
                'wpgconcert_event' => $eventlink
            ));

            return $wpdb->insert_id;
        }

        public static function get($band, $venue, $date)
        {
            global $wpdb;

            $sql = 'SELECT id from wpg_concerts'
                . ' where band = ' . $band
                . ' and venue = ' . $venue
                . ' and wpgconcert_date ="' . $date . '"';

            return $wpdb->get_results($sql);
        }
    }
}
