<?php declare(strict_types=1);
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

final class ConcertTest extends WP_UnitTestCase
{
    const VENUES = [
        [ "a venue", "Somewhere" ],
        [ "Svene Bedehus", "Svene" ],
        [ "Rockefeller Music Hall", "Oslo" ],
        [ "Sentrum Scene", "Oslo" ],
        [ "Revolver", "Oslo" ],
        [ "Meieriet", "Sogndal" ],
    ];

    const CONCERTS = [
        [ "a concert", 0, 1 ],
        [ "Concert in Svene #", 1, 4 ],
        [ "Concert at Rockefeller #", 2, 2 ],
        [ "Concert at Sentrum Scene #", 3, 4 ],
        [ "Concert at Revolver #", 4, 5 ],
        [ "Concert at Meieriet #", 5, 5 ],
    ];

    private static $concerts = [];

    /* This function runs _once_ before all the test cases.
     *
     * Use it to set up a common state that all test cases can
     * use
     */
    static function wpSetUpBeforeClass() : void
    {
        $created_venues = [];
        foreach (self::VENUES as $venue) {
            $created_venues[] = GiglogAdmin_Venue::find_or_create($venue[0], $venue[1]);
        }

        $today = date("Y-m-d");

        foreach (self::CONCERTS as $concert) {
            for ($i = 0; $i < $concert[2]; $i++) {
                if ($concert[2] > 1) {
                    $concert_name = $concert[0] . ($i + 1);
                }
                else {
                    $concert_name = $concert[0];
                }

                self::$concerts[] = GiglogAdmin_Concert::create(
                    $concert_name,
                    $created_venues[$concert[1]]->id(),
                    $today,
                    "https://example.com/tickets/42",
                    "https://example.com/events/93");
            }
        }
    }

    /* This function runs _once_ after all the test cases in this class.
     *
     * It is needed to clean up changes in the database that we don't want
     * to disturb any other tests.
     */
    static function wpTearDownAfterClass() : void
    {
        global $wpdb;

        $tables = [
            "{$wpdb->prefix}giglogadmin_concerts",
            "{$wpdb->prefix}giglogadmin_venues",
        ];

        foreach( $tables as $table ) {
            $wpdb->query("DELETE FROM {$table}");
        }
    }

    public function testCreateExistingConcertShouldFail() : void
    {
        $this->expectException(GiglogAdmin_DuplicateConcertException::class);

        $venue = GiglogAdmin_Venue::find_or_create("a venue", "Somewhere");
        $today = date("Y-m-d");

        $new = GiglogAdmin_Concert::create(
            "a concert",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");
    }

    public function testCreateExistingConcertVariableCase() : void
    {
        $this->expectException(GiglogAdmin_DuplicateConcertException::class);

        $venue = GiglogAdmin_Venue::find_or_create("a venue", "Somewhere");
        $today = date("Y-m-d");

        $new = GiglogAdmin_Concert::create(
            "a CoNceRt",
            $venue->id(),
            $today,
            "https://example.com/tickets/42",
            "https://example.com/events/93");
    }

    public function testGetConcertByIdReturnsFullConcertObject() : void
    {
        $id = self::$concerts[0]->id();
        $fetched_gig = GiglogAdmin_Concert::get($id);

        $this->assertEquals($id, $fetched_gig->id());
        $this->assertEquals("a concert", $fetched_gig->cname());
        $this->assertEquals("a venue", $fetched_gig->venue()->name());
        $this->assertEquals(GiglogAdmin_Concert::STATUS_NONE, $fetched_gig->status());
        $this->assertEquals([], $fetched_gig->roles());
    }

    public function testSetConcertStatus() : void
    {
        $id = self::$concerts[0]->id();
        $fetched_gig = GiglogAdmin_Concert::get($id);

        $fetched_gig->set_status( GiglogAdmin_Concert::STATUS_ACCRED_REQ );
        $fetched_gig->save();

        $fetched_gig_2 = GiglogAdmin_Concert::get($id);
        $this->assertEquals( GiglogAdmin_Concert::STATUS_ACCRED_REQ, $fetched_gig_2->status() );
    }

    public function testAssignConcertRoles() : void
    {
        $gig = GiglogAdmin_Concert::get(self::$concerts[0]->id());
        $gig->assign_role( 'photo1' , 'user1' );
        $gig->save();

        $fetched_gig = GiglogAdmin_Concert::get( self::$concerts[0]->id() );
        $this->assertEquals( [ 'photo1' => 'user1' ], $fetched_gig->roles() );
    }

    public function testOnlyFetchConcertsFromGivenCity() : void
    {
        $gigs_in_svene = GiglogAdmin_Concert::find_concerts([ "city" => "Svene"]);

        $this->assertEquals(4, count($gigs_in_svene));
        while ($gig = array_pop($gigs_in_svene)) {
            $this->assertEquals("Svene", $gig->venue()->city());
        }

        $gigs_in_oslo = GiglogAdmin_Concert::find_concerts(["city" => "Oslo"]);

        $this->assertEquals(11, count($gigs_in_oslo));
        while ($gig = array_pop($gigs_in_oslo)) {
            $this->assertEquals("Oslo", $gig->venue()->city());
        }

        $gigs_in_sogndal = GiglogAdmin_Concert::find_concerts(["city" => "Sogndal"]);

        $this->assertEquals(5, count($gigs_in_sogndal));
        while ($gig = array_pop($gigs_in_sogndal)) {
            $this->assertEquals("Sogndal", $gig->venue()->city());
        }
    }

    public function testOnlyFetchConcertsAtGivenVenue() : void
    {
        $venue1 = GiglogAdmin_Venue::find_or_create("Sentrum Scene", "Oslo");
        $gigs_at_ss = GiglogAdmin_Concert::find_concerts(["venue_id" => $venue1->id()]);

        $this->assertEquals(4, count($gigs_at_ss));
        while ($gig = array_pop($gigs_at_ss)) {
            $this->assertEquals("Sentrum Scene", $gig->venue()->name());
        }

        $venue2 = GiglogAdmin_Venue::find_or_create("Rockefeller Music Hall", "Oslo");
        $gigs_at_rmh = GiglogAdmin_Concert::find_concerts(["venue_id" => $venue2->id()]);

        $this->assertEquals(2, count($gigs_at_rmh));
        while ($gig = array_pop($gigs_at_rmh)) {
            $this->assertEquals("Rockefeller Music Hall", $gig->venue()->name());
        }

        $venue3 = GiglogAdmin_Venue::find_or_create("Revolver", "Oslo");
        $gigs_at_r = GiglogAdmin_Concert::find_concerts(["venue_id" => $venue3->id()]);

        $this->assertEquals(5, count($gigs_at_r));
        while ($gig = array_pop($gigs_at_r)) {
            $this->assertEquals("Revolver", $gig->venue()->name());
        }
    }

    public function testFetchAllConcerts() : void
    {
        $gigs = GiglogAdmin_Concert::find_concerts();
        $this->assertEquals(count(self::$concerts), count($gigs));
    }

    public function testFetchConcertByNameVenueAndDate() : void
    {
        $gigs = GiglogAdmin_Concert::find_concerts([
            'name' => 'a concert',
            'venue' => 'a venue',
            'date' => date('Y-m-d')
        ]);

        $this->assertEquals(1, count($gigs));

        $gig = array_shift($gigs);
        $this->assertEquals('a concert', $gig->cname());
        $this->assertEquals('a venue', $gig->venue()->name());
        $this->assertEquals(date('Y-m-d'), explode(' ', $gig->cdate())[0]);
    }
}
