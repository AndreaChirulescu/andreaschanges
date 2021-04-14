<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require 'tests/stubs/wpdb_stub.php';
require 'includes/venue.php';

final class VenueTest extends TestCase
{
    public function testCreatingVenueWithName(): void
    {
        $venue = GiglogAdmin_Venue::create("Svene Samfunns- og Bedehus");
        $this->assertEquals("Svene Samfunns- og Bedehus", $venue->name());
        $this->assertEquals(1, $venue->id());
    }

    public function testFindOrCreateNonExistingVenue() : void
    {
        $venue = GiglogAdmin_Venue::find_or_create("Svene Samfunns- og Bedehus");
        $this->assertEquals("Svene Samfunns- og Bedehus", $venue->name());
        $this->assertEquals(1, $venue->id());
    }

    public function testFindOrCreateExistingVenue() : void
    {
        global $wpdb;

        $v = new stdClass();
        $v->id = 42;
        $v->wpgvenue_name = 'Slarkhaillen';
        $v->wpgvenue_city = 'Ofoten';
        $v->wpgvenue_address = 'Baillsvingen 4';
        $v->wpgvenue_webpage = 'https://slarkhaillen.no';

        $wpdb = $this->createStub(wpdb::class);
        $wpdb->method('get_results')->willReturn(array($v));
        $venue = GiglogAdmin_Venue::find_or_create("Slarkhaillen");

        $this->assertEquals(42, $venue->id());
        $this->assertEquals($v->wpgvenue_name, $venue->name());
    }

    public function testFindAllVenuesInCity() : void
    {
        global $wpdb;

        $results = array();
        for ($i = 0; $i < 3; $i++) {
            $results[$i] = new stdClass();
            $results[$i]->id = 42 + $i;
            $results[$i]->wpgvenue_name = "Venue #" . $i;
            $results[$i]->wpgvenue_city = "Osaka";
        }

        $wpdb = $this->createStub(wpdb::class);
        $wpdb->method('prepare')->willReturn("prepared");
        $wpdb->method('get_results')->willReturn($results);

        $venues = GiglogAdmin_Venue::venues_in_city("Osaka");

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals("Osaka", $venues[$i]->city());
            $this->assertEquals("Venue #" . $i, $venues[$i]->name());
            $this->assertEquals(42 + $i, $venues[$i]->id());
        }
    }

    public function testFindAllVenues() : void
    {
        global $wpdb;

        $results = array();
        for ($i = 0; $i < 3; $i++) {
            $results[$i] = new stdClass();
            $results[$i]->id = 42 + $i;
            $results[$i]->wpgvenue_name = "Venue #" . $i;
            $results[$i]->wpgvenue_city = "City #" . $i;
        }

        $wpdb = $this->createStub(wpdb::class);
        $wpdb->method('get_results')->willReturn($results);

        $venues = GiglogAdmin_Venue::all_venues();

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals("City #" . $i, $venues[$i]->city());
            $this->assertEquals("Venue #" . $i, $venues[$i]->name());
            $this->assertEquals(42 + $i, $venues[$i]->id());
        }
    }
}
