<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

require __DIR__ . '/../includes/band.php';

final class BandTest extends WP_UnitTestCase
{
    public function testCreatingBandWithName(): void
    {
        $count = count(GiglogAdmin_Band::all_bands());

        $band = GiglogAdmin_Band::create("The Flamboyant Blasphemers");

        $this->assertEquals("The Flamboyant Blasphemers", $band->bandname());
        $this->assertEquals($count + 1, count(GiglogAdmin_Band::all_bands()));
    }
}
