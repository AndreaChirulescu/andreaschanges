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
        $this->assertEquals($venue->id(), $concert->venue());
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
}
