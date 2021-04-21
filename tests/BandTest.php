<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

require __DIR__ . '/../includes/band.php';

final class BandTest extends WP_UnitTestCase
{
    /* This function runs _once_ before all the test cases.
     *
     * Use it to set up a common state that all test cases can
     * use
     */
    static function wpSetUpBeforeClass() : void
    {
        GiglogAdmin_Band::create("The Flamboyant Blasphemers");
    }

    public function testCreatingBandWithName() : void
    {
        $count = count(GiglogAdmin_Band::all_bands());

        $band = GiglogAdmin_Band::create("Tullerusk");

        $this->assertEquals("Tullerusk", $band->bandname());
        $this->assertEquals($count + 1, count(GiglogAdmin_Band::all_bands()));
    }

    public function testCreateExistingBand() : void
    {
        $count = count(GiglogAdmin_Band::all_bands());

        $existing_band = GiglogAdmin_Band::find("The Flamboyant Blasphemers", "NO");
        $new_band = GiglogAdmin_Band::create("The Flamboyant Blasphemers");

        $this->assertEquals($count, count(GiglogAdmin_Band::all_bands()));
        $this->assertEquals($existing_band->id(), $new_band->id());
        $this->assertEquals($existing_band->bandname(), $new_band->bandname());
    }

    public function testCreateBandsWithSameNameInDifferentCountry() : void
    {
        $existing_band = GiglogAdmin_Band::find("The Flamboyant Blasphemers", "NO");
        $new_band = GiglogAdmin_Band::create("The Flamboyant Blasphemers", "RO");

        $this->assertNotEquals($existing_band->id(), $new_band->id());
    }

    public function testFindExistingBandReturnsObject() : void
    {
        $found = GiglogAdmin_Band::find("The Flamboyant Blasphemers", "NO");

        $this->assertNotNull($found);
        $this->assertEquals("The Flamboyant Blasphemers", $found->bandname());
    }

    public function testFindNonExistingBandReturnsNULL() : void
    {
        // Nice, UK isn't in the country list, so let's move Venom to Azerbajan
        // for now...
        $found = GiglogAdmin_Band::find("Venom", "AZ");

        $this->assertNull($found);
    }
}
