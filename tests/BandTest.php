<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

require __DIR__ . '/../includes/band.php';

final class BandTest extends WP_UnitTestCase
{
    public function testCreatingBandWithName() : void
    {
        $count = count(GiglogAdmin_Band::all_bands());

        $band = GiglogAdmin_Band::create("The Flamboyant Blasphemers");

        $this->assertEquals("The Flamboyant Blasphemers", $band->bandname());
        $this->assertEquals($count + 1, count(GiglogAdmin_Band::all_bands()));
    }

    public function testCreateExistingBand() : void
    {
        $band1 = GiglogAdmin_Band::create("The Flamboyant Blasphemers");
        $band2 = GiglogAdmin_Band::create("The Flamboyant Blasphemers");

        $this->assertEquals($band1->id(), $band2->id());
    }

    public function testCreateBandsWithSameNameInDifferentCountry() : void
    {
        $band1 = GiglogAdmin_Band::create("The Flamboyant Blasphemers", "RO");
        $band2 = GiglogAdmin_Band::create("The Flamboyant Blasphemers", "NO");

        $this->assertNotEquals($band1->id(), $band2->id());
    }

    public function testFindExistingBandReturnsObject() : void
    {
        $created = GiglogAdmin_Band::create("The Flamboyant Blasphemers", "RO");
        $found = GiglogAdmin_Band::find("The Flamboyant Blasphemers", "RO");

        $this->assertNotNull($found);
        $this->assertEquals($created->id(), $found->id());
    }

    public function testFindNonExistingBandReturnsNULL() : void
    {
        $band1 = GiglogAdmin_Band::create("The Flamboyant Blasphemers", "RO");

        // Nice, UK isn't in the country list, so let's move Venom to Azerbajan
        // for now...
        $found = GiglogAdmin_Band::find("Venom", "AZ");

        $this->assertNull($found);
    }
}
