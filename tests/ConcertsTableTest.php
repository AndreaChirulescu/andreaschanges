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
        global $current_screen;
        global $current_user;

        $current_user = $this->factory()->user->create_and_get(['role' => 'administrator']);
        $oldscreen = $current_screen;
        $current_screen = WP_Screen::get( 'admin_init' );

        $c = new GiglogAdmin_ConcertsTable();
        $html = $c->render();

        $current_screen = $oldscreen;

        $doc = DOMDocument::loadHTML($html);
        $forms = $doc->getElementsByTagName('form');

        $assignit_count = 0;
        $adminactions_count = 0;

        foreach ($forms as $form) {
            $cls = $form->attributes->getNamedItem('class')->nodeValue;
            if ($cls == 'assign_concert' || $cls == 'unassign_concert') {
                $assignit_count++;
            }

            if ($cls == 'adminactions') {
                $adminactions_count++;
            }
        }

        $this->assertEquals(64, $assignit_count);       // four for each gig
        $this->assertEquals(16, $adminactions_count);   // once for each gig
    }

    function testDontShowAdminOnlyControlsToNonAdminsOnAdminPage() {
        global $current_screen;
        global $current_user;

        $current_user = $this->factory()->user->create_and_get(['role' => 'editor']);
        $oldscreen = $current_screen;
        $current_screen = WP_Screen::get( 'admin_init' );

        $c = new GiglogAdmin_ConcertsTable();
        $html = $c->render();

        $current_screen = $oldscreen;

        $doc = DOMDocument::loadHTML($html);
        $forms = $doc->getElementsByTagName('form');

        $assignit_count = 0;
        $adminactions_count = 0;

        foreach ($forms as $form) {
            $cls = $form->attributes->getNamedItem('class')->nodeValue;
            if ($cls == 'assign_concert' || $cls == 'unassign_concert') {
                $assignit_count++;
            }

            if ($cls == 'adminactions') {
                $adminactions_count++;
            }
        }

        $this->assertEquals(64, $assignit_count);       // four for each gig
        $this->assertEquals(0, $adminactions_count);   // once for each gig
    }

    function testDontShowAnyControlsIfNotOnAdminPage() {
        global $current_user;

        // "log in" as administrator to make sure no admin side stuff is
        // rendered on the public site, even if we're a high privilege user.
        $current_user = $this->factory()->user->create_and_get(['role' => 'administrator']);

        $c = new GiglogAdmin_ConcertsTable();
        $html = $c->render();

        $doc = DOMDocument::loadHTML($html);
        $forms = $doc->getElementsByTagName('form');

        $assignit_count = 0;
        $adminactions_count = 0;

        foreach ($forms as $form) {
            $cls = $form->attributes->getNamedItem('class')->nodeValue;
            if ($cls == 'assign_concert' || $cls == 'unassign_concert') {
                $assignit_count++;
            }

            if ($cls == 'adminactions') {
                $adminactions_count++;
            }
        }

        $this->assertEquals(0, $assignit_count);       // four for each gig
        $this->assertEquals(0, $adminactions_count);   // once for each gig
    }
}
