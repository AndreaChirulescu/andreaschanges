<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

require __DIR__ . '/../includes/venue.php';

final class VenueTest extends WP_UnitTestCase
{
    public function testCreatingVenueWithName(): void
    {
        $count = count(GiglogAdmin_Venue::all_venues());

        $venue = GiglogAdmin_Venue::create("Svene Samfunns- og Bedehus");
        $this->assertEquals("Svene Samfunns- og Bedehus", $venue->name());

        $this->assertEquals($count + 1, count(GiglogAdmin_Venue::all_venues()));
    }

    public function testFindOrCreateNonExistingVenue() : void
    {
        $count = count(GiglogAdmin_Venue::all_venues());

        $venue = GiglogAdmin_Venue::find_or_create("Svene Samfunns- og Bedehus");
        $this->assertEquals("Svene Samfunns- og Bedehus", $venue->name());

        $this->assertEquals($count + 1, count(GiglogAdmin_Venue::all_venues()));
    }

    public function testFindOrCreateExistingVenue() : void
    {
        global $wpdb;

        $venue = GiglogAdmin_Venue::create("Svene Samfunns- og Bedehus");
        $other = GiglogAdmin_Venue::find_or_create("Svene Samfunns- og Bedehus");

        $this->assertEquals($other->id(), $venue->id());
        $this->assertEquals($other->name(), $venue->name());
    }

    public function testFindOrCreateExistingVenueVariableCase() : void
    {
        global $wpdb;

        $venue = GiglogAdmin_Venue::create("This is not the venue you are looking for");
        $other = GiglogAdmin_Venue::find_or_create("ThiS IS noT tHe VenuE YOu aRe looking FoR");

        $this->assertEquals($other->id(), $venue->id());
        $this->assertEquals($other->name(), $venue->name());
    }

    public function testFindAllVenuesInCity() : void
    {
        global $wpdb;

        for ($i = 0; $i < 3; $i++) {
            GiglogAdmin_Venue::create("Venue in Osaka #" . $i, "Osaka");
        }

        for ($i = 0; $i < 5; $i++) {
            GiglogAdmin_Venue::create("Venue in Berlin #" . $i, "Berlin");
        }

        for ($i = 0; $i < 2; $i++) {
            GiglogAdmin_Venue::create("Venue in Svene #" . $i, "Svene");
        }

        $venues_in_osaka = GiglogAdmin_Venue::venues_in_city("Osaka");
        $venues_in_berlin = GiglogAdmin_Venue::venues_in_city("Berlin");
        $venues_in_svene = GiglogAdmin_Venue::venues_in_city("Svene");

        $this->assertEquals(3, count($venues_in_osaka));
        $this->assertEquals(5, count($venues_in_berlin));
        $this->assertEquals(2, count($venues_in_svene));
    }

    public function testFindAllVenues() : void
    {
        global $wpdb;

        for ($i = 0; $i < 3; $i++) {
            GiglogAdmin_Venue::create("Venue #" . $i);
        }

        $venues = GiglogAdmin_Venue::all_venues();
        $this->assertEquals(3, count($venues));

    }
}
