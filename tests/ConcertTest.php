<?php declare(strict_types=1);
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

final class ConcertTest extends WP_UnitTestCase
{
    public function testCreateConcert() : void
    {
        $venue = GiglogAdmin_Venue::create("a venue");
        $today = date("Y-m-d");

        $concert = GiglogAdmin_Concert::create(
            "a concert",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $this->assertEquals("a concert", $concert->cname());
        $this->assertEquals($venue->id(), $concert->venue()->id());
        $this->assertEquals($today, $concert->cdate());
        $this->assertEquals("https://example.com/tickets/42", $concert->tickets());
        $this->assertEquals("https://example.com/events/93", $concert->eventlink());
    }

    public function testCreateExistingConcert() : void
    {
        $venue = GiglogAdmin_Venue::create("a venue");
        $today = date("Y-m-d");

        GiglogAdmin_Concert::create(
            "a concert",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $new = GiglogAdmin_Concert::create(
            "a concert",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $this->assertNull($new);
    }

    public function testCreateExistingConcertVariableCase() : void
    {
        $venue = GiglogAdmin_Venue::create("a venue");
        $today = date("Y-m-d");

        GiglogAdmin_Concert::create(
            "a concert123",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $new = GiglogAdmin_Concert::create(
            "a CoNceRt123",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $this->assertNull($new);
    }

    public function testGetConcertByIdReturnsFullConcertObject() : void
    {
        $venue = GiglogAdmin_Venue::create("a venue");
        $today = date("Y-m-d");

        $gig = GiglogAdmin_Concert::create(
            "a concert123",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");

        $fetched_gig = GiglogAdmin_Concert::get($gig->id());

        $this->assertEquals($gig->id(), $fetched_gig->id());
        $this->assertEquals($gig->cname(), $fetched_gig->cname());
        $this->assertEquals($venue->id(), $fetched_gig->venue()->id());
    }

    public function testOnlyFetchConcertsFromGivenCity() : void
    {
        $venue1 = GiglogAdmin_Venue::create("Svene Bedehus", "Svene");
        $venue2 = GiglogAdmin_Venue::create("Rockefeller Music Hall", "Oslo");
        $venue3 = GiglogAdmin_Venue::create("Meieriet", "Sogndal");

        for ($i = 0; $i < 4; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue1->id(), '', '', '');
        }

        for ($i = 4; $i < 6; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue2->id(), '', '', '');
        }

        for ($i = 6; $i < 11; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue3->id(), '', '', '');
        }

        $gigs_in_svene = GiglogAdmin_Concert::find_concerts_in("Svene");

        $this->assertEquals(4, count($gigs_in_svene));
        while ($gig = array_pop($gigs_in_svene)) {
            $this->assertEquals("Svene", $gig->venue()->city());
        }


        $gigs_in_oslo = GiglogAdmin_Concert::find_concerts_in("Oslo");

        $this->assertEquals(2, count($gigs_in_oslo));
        while ($gig = array_pop($gigs_in_oslo)) {
            $this->assertEquals("Oslo", $gig->venue()->city());
        }

        $gigs_in_sogndal = GiglogAdmin_Concert::find_concerts_in("Sogndal");

        $this->assertEquals(5, count($gigs_in_sogndal));
        while ($gig = array_pop($gigs_in_sogndal)) {
            $this->assertEquals("Sogndal", $gig->venue()->city());
        }
    }

    public function testOnlyFetchConcertsAtGivenVenue() : void
    {
        $venue1 = GiglogAdmin_Venue::create("Sentrum Scene", "Oslo");
        $venue2 = GiglogAdmin_Venue::create("Rockefeller Music Hall", "Oslo");
        $venue3 = GiglogAdmin_Venue::create("Revolver", "Oslo");

        for ($i = 0; $i < 4; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue1->id(), '', '', '');
        }

        for ($i = 4; $i < 6; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue2->id(), '', '', '');
        }

        for ($i = 6; $i < 11; $i++) {
            GiglogAdmin_Concert::create('Concert ' . $i, $venue3->id(), '', '', '');
        }

        $gigs_at_ss = GiglogAdmin_Concert::find_concerts_at($venue1);

        $this->assertEquals(4, count($gigs_at_ss));
        while ($gig = array_pop($gigs_at_ss)) {
            $this->assertEquals("Sentrum Scene", $gig->venue()->name());
        }

        $gigs_at_rmh = GiglogAdmin_Concert::find_concerts_at($venue2);

        $this->assertEquals(2, count($gigs_at_rmh));
        while ($gig = array_pop($gigs_at_rmh)) {
            $this->assertEquals("Rockefeller Music Hall", $gig->venue()->name());
        }

        $gigs_at_r = GiglogAdmin_Concert::find_concerts_at($venue3);

        $this->assertEquals(5, count($gigs_at_r));
        while ($gig = array_pop($gigs_at_r)) {
            $this->assertEquals("Revolver", $gig->venue()->name());
        }
    }
}
