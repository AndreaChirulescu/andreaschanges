<?php declare(strict_types=1);
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

class ConcertsTableTest extends WP_UnitTestCase
{
    const VENUES = [
        [ "Rockefeller Music Hall", "Oslo" ],
        [ "Sentrum Scene", "Oslo" ],
        [ "Revolver", "Oslo" ],
        [ "Meieriet", "Sogndal" ],
    ];

    const CONCERTS = [
        [ "Concert at Rockefeller #", 0, 2 ],
        [ "Concert at Sentrum Scene #", 1, 4 ],
        [ "Concert at Revolver #", 2, 5 ],
        [ "Concert at Meieriet #", 3, 5 ],
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
            "wpg_concerts",
            "wpg_venues",
        ];

        foreach( $tables as $table ) {
            $wpdb->query("DELETE FROM {$table}");
        }
    }

    function testShowAllControlsToAdminOnAdminPage() {
        $c = new GiglogAdmin_ConcertsTable();
        $html = $c->render();
        //$this->assertEquals('balle', $html);

        $doc = DOMDocument::loadHTML($html);
        $forms = $doc->getElementsByTagName('form');
        $count = 0;
        foreach ($forms as $form) {
            $cls = $form->attributes->getNamedItem('class')->nodeValue;
            if ($cls == 'assignit' || $cls == 'unassignit') {
                $count++;
            }
        }

        $this->assertEquals($count, 64);
    }
}
